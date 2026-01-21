<?php

/**
 * Abstract base class for PHPUnit test cases using Mink.
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2025.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  Tests
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeebyTest\Integration;

use Behat\Mink\Element\Element;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Element\TraversableElement;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;

use function call_user_func;
use function floatval;
use function intval;
use function is_string;

/**
 * Abstract base class for PHPUnit test cases using Mink.
 *
 * @category GeebyDeeby
 * @package  Tests
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
abstract class MinkTestCase extends \PHPUnit\Framework\TestCase
{
    use \GeebyDeebyTest\Feature\LiveDetectionTrait;

    public const DEFAULT_TIMEOUT = 5000;

    /**
     * Mink session
     *
     * @var Session
     */
    protected $session;

    /**
     * Get name of the current test
     *
     * @return string
     */
    protected function getTestName(): string
    {
        return $this::class . '::' . $this->nameWithDataSet();
    }

    /**
     * Go to the specified Geeby-Deeby page.
     *
     * @param string $path Path to load.
     *
     * @return TraversableElement
     */
    protected function goToPage(string $path = ''): TraversableElement
    {
        $session = $this->getMinkSession();
        $session->visit($this->getGeebyDeebyUrl($path));
        return $session->getPage();
    }

    /**
     * Sleep if necessary.
     *
     * @param int $secs Seconds to sleep
     *
     * @return void
     */
    protected function snooze($secs = 1)
    {
        $snoozeMultiplier = $this->getSnoozeMultiplier();
        if ($snoozeMultiplier <= 0) {
            $snoozeMultiplier = 1;
        }
        usleep(1000000 * $secs * $snoozeMultiplier);
    }

    /**
     * Get the snooze multiplier.
     *
     * @return float
     */
    protected function getSnoozeMultiplier(): float
    {
        return floatval(getenv('GBDB_SNOOZE_MULTIPLIER'));
    }

    /**
     * Get the default timeout in milliseconds
     *
     * @return int
     */
    protected function getDefaultTimeout(): int
    {
        return intval(
            getenv('GBDB_DEFAULT_TEST_TIMEOUT') ?: self::DEFAULT_TIMEOUT
        );
    }

    /**
     * Test an element for visibility.
     *
     * @param NodeElement $element Element to test
     *
     * @return bool
     */
    protected function checkVisibility(NodeElement $element)
    {
        return $element->isVisible();
    }

    /**
     * Get the Mink driver, initializing it if necessary.
     *
     * @return \Behat\Mink\Driver\CoreDriver
     */
    protected function getMinkDriver()
    {
        return new ChromeDriver('http://localhost:9222', null, 'data:;');
    }

    /**
     * Get a Mink session.
     *
     * @return Session
     */
    protected function getMinkSession(): Session
    {
        if (empty($this->session)) {
            $this->session = new Session($this->getMinkDriver());
            $this->session->start();
        }
        return $this->session;
    }

    /**
     * Shut down the Mink session.
     *
     * @return void
     */
    protected function stopMinkSession()
    {
        if (!empty($this->session)) {
            $this->session->stop();
            $this->session = null;
        }
    }

    /**
     * Get base URL of running Geeby-Deeby instance.
     *
     * @param string $path Relative path to add to base URL.
     *
     * @return string
     */
    protected function getGeebyDeebyUrl($path = '')
    {
        $base = getenv('GBDB_URL');
        if (empty($base)) {
            $base = 'http://localhost';
        }
        return $base . $path;
    }

    /**
     * Wait for an element to exist, then retrieve it.
     *
     * @param Element $page     Page element
     * @param string  $selector CSS selector
     * @param int     $timeout  Wait timeout (in ms)
     * @param int     $index    Index of the element (0-based)
     *
     * @return mixed
     */
    protected function findCss(
        Element $page,
        $selector,
        $timeout = null,
        $index = 0
    ) {
        $timeout ??= $this->getDefaultTimeout();
        $session = $this->getMinkSession();
        $session->wait(
            $timeout,
            "document.querySelectorAll('$selector').length > $index"
        );
        $results = $page->findAll('css', $selector);
        $this->assertIsArray($results, "Selector not found: $selector");
        $result = $results[$index] ?? null;
        $this->assertIsObject(
            $result,
            "Element not found: $selector index $index"
        );
        return $result;
    }

    /**
     * Wait for a JavaScript statement to result in true.
     *
     * Includes a check for $ to be available to make sure jQuery has been loaded.
     *
     * @param string $statement JavaScript statement to evaluate
     * @param int    $timeout   Wait timeout (in ms)
     *
     * @return mixed
     */
    protected function waitStatement($statement, $timeout = null)
    {
        $timeout ??= $this->getDefaultTimeout();
        $session = $this->getMinkSession();
        $this->assertTrue(
            $session->wait(
                $timeout,
                "(typeof $ !== 'undefined') && ($statement)"
            ),
            "Statement '$statement'"
        );
    }

    /**
     * Wait for an element to NOT exist.
     *
     * @param Element $page     Page element
     * @param string  $selector CSS selector
     * @param int     $timeout  Wait timeout (in ms)
     * @param int     $index    Index of the element (0-based)
     *
     * @return void
     */
    protected function unFindCss(
        Element $page,
        $selector,
        $timeout = null,
        $index = 0
    ) {
        $timeout ??= $this->getDefaultTimeout();
        $startTime = microtime(true);
        $exception = null;
        while ((microtime(true) - $startTime) * 1000 <= $timeout) {
            try {
                $elements = $page->findAll('css', $selector);
                if (!isset($elements[$index])) {
                    // Assert so that this method can be the only check in a test
                    // without it being marked as risky with the message
                    // "This test did not perform any assertions". Also makes this
                    // check count as an assertion in test statistics.
                    $this->assertNull(null);
                    return;
                }
            } catch (\Exception $e) {
                // This may happen e.g. if the page is reloaded right in the middle
                // due to an event. Store the exception and throw later if we don't
                // succeed with retries:
                $exception ??= $e;
            }
            usleep(50000);
        }
        if (null !== $exception) {
            throw $exception;
        }

        throw new \Exception("Selector '$selector' remains accessible");
    }

    /**
     * Click on a CSS element.
     *
     * @param Element $page     Page element
     * @param string  $selector CSS selector
     * @param int     $timeout  Wait timeout (in ms)
     * @param int     $index    Index of the element (0-based)
     *
     * @return mixed
     */
    protected function clickCss(
        Element $page,
        $selector,
        $timeout = null,
        $index = 0
    ) {
        $maxTries = 3;
        for ($tries = 1; $tries <= $maxTries; $tries++) {
            try {
                $result = $this->findCss($page, $selector, $timeout, $index);
                $result->click();
                return $result;
            } catch (\Exception $e) {
                // This may happen e.g. if the page is reloaded right in the middle
                // due to an event. Snooze and retry unless this is the last loop:
                if ($tries === $maxTries) {
                    throw $e;
                }
                $this->snooze();
            }
        }
        throw new \Exception('Unexpected state reached.');
    }

    /**
     * Set a value within an element selected via CSS; retry if set fails
     * due to browser bugs.
     *
     * @param Element $page        Page element
     * @param string  $selector    CSS selector
     * @param string  $value       Value to set
     * @param int     $timeout     Wait timeout for CSS selection (in ms)
     * @param int     $retries     Retry count for set loop
     * @param bool    $verifyValue Whether to verify that the value was written
     * @param bool    $reFocus     Whether to focus the element when done setting the value
     *
     * @return mixed
     */
    protected function findCssAndSetValue(
        Element $page,
        $selector,
        $value,
        $timeout = null,
        $retries = 6,
        $verifyValue = true,
        $reFocus = false
    ) {
        $timeout ??= $this->getDefaultTimeout();

        // Workaround for Chromedriver bug; sometimes setting a value
        // doesn't work on the first try.
        for ($i = 1; $i <= $retries; $i++) {
            try {
                $field = $this->findCss($page, $selector, $timeout, 0);
                $field->setValue($value);
                // Did it work? If so, we're done and can leave....
                if (
                    !$verifyValue
                    || $field->getValue() === $value
                ) {
                    if ($reFocus) {
                        $field->focus();
                    }
                    return;
                }

                $this->logWarning(
                    'RETRY setValue after failure in ' . $this->getTestName()
                    . " (try $i)."
                );
            } catch (\Exception $e) {
                $this->logWarning(
                    'RETRY setValue after exception in ' . $this->getTestName()
                    . " (try $i): " . (string)$e
                );
            }

            $this->snooze();
        }

        throw new \Exception('Failed to set value using ' . $selector . ' after ' . $retries . ' attempts.');
    }

    /**
     * Get text of an element selected via CSS; retry if it fails due to DOM change.
     *
     * @param Element $page     Page element
     * @param string  $selector CSS selector
     * @param int     $timeout  Wait timeout for CSS selection (in ms)
     * @param int     $index    Index of the element (0-based)
     * @param int     $retries  Retry count for set loop
     *
     * @return string
     */
    protected function findCssAndGetText(
        Element $page,
        $selector,
        $timeout = null,
        $index = 0,
        $retries = 6
    ) {
        return $this->findCssAndCallMethod($page, $selector, 'getText', $timeout, $index, $retries);
    }

    /**
     * Get the current value of a <select> control (or null if not found).
     *
     * @param Element $page     Page containing element
     * @param string  $selector Selector targeting element
     *
     * @return ?string
     */
    protected function getSelectedOption(Element $page, string $selector): ?string
    {
        $options = $page->findAll('css', $selector . ' option');
        $selected = $options[0] ?? null;
        foreach ($options as $next) {
            if ($next->isSelected()) {
                $selected = $next;
                break;
            }
        }
        return $selected?->getValue();
    }

    /**
     * Get value of an element selected via CSS; retry if it fails due to DOM change.
     *
     * @param Element $page     Page element
     * @param string  $selector CSS selector
     * @param int     $timeout  Wait timeout for CSS selection (in ms)
     * @param int     $index    Index of the element (0-based)
     * @param int     $retries  Retry count for set loop
     *
     * @return string
     */
    protected function findCssAndGetValue(
        Element $page,
        $selector,
        $timeout = null,
        $index = 0,
        $retries = 6
    ) {
        return $this->findCssAndCallMethod($page, $selector, 'getValue', $timeout, $index, $retries);
    }

    /**
     * Get text of an element selected via CSS; retry if it fails due to DOM change.
     *
     * @param Element $page     Page element
     * @param string  $selector CSS selector
     * @param int     $timeout  Wait timeout for CSS selection (in ms)
     * @param int     $index    Index of the element (0-based)
     * @param int     $retries  Retry count for set loop
     *
     * @return string
     */
    protected function findCssAndGetHtml(
        Element $page,
        $selector,
        $timeout = null,
        $index = 0,
        $retries = 6
    ) {
        return $this->findCssAndCallMethod($page, $selector, 'getHtml', $timeout, $index, $retries);
    }

    /**
     * Return value of a method of an element selected via CSS; retry if it fails due to DOM change.
     *
     * @param Element         $page     Page element
     * @param string          $selector CSS selector
     * @param string|callable $method   Node's method to call (string) or callable that gets the node as parameter
     * @param int             $timeout  Wait timeout for CSS selection (in ms)
     * @param int             $index    Index of the element (0-based)
     * @param int             $retries  Retry count for set loop
     *
     * @return string
     */
    protected function findCssAndCallMethod(
        Element $page,
        $selector,
        $method,
        $timeout = null,
        $index = 0,
        $retries = 6,
    ) {
        $timeout ??= $this->getDefaultTimeout();

        for ($i = 1; $i <= $retries; $i++) {
            try {
                $element = $this->findCss($page, $selector, $timeout, $index);
                return is_string($method) ? call_user_func([$element, $method]) : $method($element);
            } catch (\Exception $e) {
                $this->logWarning(
                    'RETRY findCssAndGetText after exception in ' . $this->getTestName()
                    . " (try $i): " . (string)$e
                );
            }

            $this->snooze();
        }

        throw new \Exception("Failed to call $method on '$selector' after $retries attempts.");
    }

    /**
     * Retrieve a link and assert that it exists before returning it.
     *
     * @param TraversableElement $page Page element
     * @param string             $text Link text to match
     *
     * @return mixed
     */
    protected function findAndAssertLink(TraversableElement $page, $text)
    {
        $link = $page->findLink($text);
        $this->assertIsObject($link);
        return $link;
    }

    /**
     * Check whether an element containing the specified text exists.
     *
     * @param Element $page     Page element
     * @param string  $selector CSS selector
     * @param string  $text     Expected text
     *
     * @return bool
     */
    protected function hasElementsMatchingText(Element $page, $selector, $text)
    {
        foreach ($page->findAll('css', $selector) as $current) {
            if ($text === $current->getText()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check that a field content is valid (does not have the :invalid pseudo class).
     *
     * @param Element $page     Page element (not currently used)
     * @param string  $selector CSS selector
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function checkFieldIsValid(Element $page, string $selector): void
    {
        $session = $this->getMinkSession();
        $session->wait(
            $this->getDefaultTimeout(),
            "document.querySelector('$selector:invalid') === null"
        );
    }

    /**
     * Check that a field content is invalid (has the :invalid pseudo class).
     *
     * @param Element $page     Page element (not currently used)
     * @param string  $selector CSS selector
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function checkFieldIsInvalid(Element $page, string $selector): void
    {
        $session = $this->getMinkSession();
        $session->wait(
            $this->getDefaultTimeout(),
            "document.querySelector('$selector:invalid') !== null"
        );
    }

    /**
     * Wait for a callback to return the expected value
     *
     * @param mixed    $expected    Expected value
     * @param callable $callback    Callback used to get the results
     * @param callable $compareFunc Callback used to compare the results
     * @param callable $assertion   Assertion to make
     * @param ?int     $timeout     Wait timeout (in ms)
     *
     * @return void
     */
    protected function assertWithTimeout(
        $expected,
        callable $callback,
        callable $compareFunc,
        callable $assertion,
        ?int $timeout = null
    ) {
        $timeout ??= $this->getDefaultTimeout();
        $result = null;
        $startTime = microtime(true);
        $exception = null;
        while ((microtime(true) - $startTime) * 1000 <= $timeout) {
            try {
                $result = $callback();
                if (call_user_func($compareFunc, $expected, $result)) {
                    // Ignore any previous exception since the callback succeeded eventually:
                    $exception = null;
                    break;
                }
            } catch (\Exception $e) {
                // Defer throwing the exception:
                $exception = $e;
            }
            usleep(100000);
        }
        if ($exception) {
            throw $exception;
        }
        call_user_func($assertion, $expected, $result);
    }

    /**
     * Wait for a callback to return the expected value
     *
     * @param mixed    $expected Expected value
     * @param callable $callback Callback
     * @param ?int     $timeout  Wait timeout (in ms)
     *
     * @return void
     */
    protected function assertEqualsWithTimeout(
        $expected,
        callable $callback,
        ?int $timeout = null
    ) {
        $this->assertWithTimeout(
            $expected,
            $callback,
            function ($expected, $result): bool {
                return $expected === $result;
            },
            [$this, 'assertEquals'],
            $timeout
        );
    }

    /**
     * Wait for a callback to return a string containing the expected value
     *
     * @param string   $expected Expected value
     * @param callable $callback Callback
     * @param ?int     $timeout  Wait timeout (in ms)
     *
     * @return void
     */
    protected function assertStringContainsStringWithTimeout(
        string $expected,
        callable $callback,
        ?int $timeout = null
    ) {
        $this->assertWithTimeout(
            $expected,
            $callback,
            function (string $expected, string $result): bool {
                return str_contains($result, $expected);
            },
            [$this, 'assertStringContainsString'],
            $timeout
        );
    }

    /**
     * Wait for page load (full page or any element) to complete
     *
     * @param Element $page    Page element
     * @param ?int    $timeout Wait timeout (in ms)
     *
     * @return void
     */
    protected function waitForPageLoad(
        Element $page,
        ?int $timeout = null
    ) {
        $timeout ??= $this->getDefaultTimeout();
        $session = $this->getMinkSession();
        // Wait for page load to complete:
        $session->wait($timeout, "document.readyState === 'complete'");
        // Wait for any AJAX requests to complete (and that jQuery is loaded):
        $session->wait(
            $timeout,
            "typeof $ !== 'undefined' && $.active === 0"
        );
        // Wait for modal load to complete:
        $this->unFindCss($page, '.modal-loading-overlay', $timeout);
        // Wait for page load to complete again in case it was triggered by
        // lightbox refresh or similar:
        $session->wait($timeout, "document.readyState === 'complete'");
        // Make sure any loading spinners are not visible (and jQuery is still loaded):
        $session->wait(
            $timeout,
            "typeof $ !== 'undefined' && $('.loading-spinner:visible').length === 0"
        );
        // Make sure nothing is being animated (and jQuery is still loaded):
        $jqueryOk = $session->wait(
            $timeout,
            "typeof $ !== 'undefined' && $(':animated').length === 0"
        );
        if ($jqueryOk) {
            // Finally, make sure all jQuery ready handlers are done:
            $session->evaluateScript(
                <<<EOS
                    if (window.__documentIsReady !== true) {
                        $(document).ready(function() { window.__documentIsReady = true; });
                    }
                    EOS
            );
            $session->wait(
                $timeout,
                'window.__documentIsReady === true'
            );
        }
    }

    /**
     * Log a warning message
     *
     * @param string $consoleMsg Message to output to console
     * @param string $logMsg     Message to output to PHP error log
     *
     * @return void
     */
    protected function logWarning(string $consoleMsg, string $logMsg = ''): void
    {
        file_put_contents('php://stderr', PHP_EOL . $consoleMsg . PHP_EOL);
        if ($logMsg) {
            error_log($logMsg);
        }
    }

    /**
     * Log in as a user.
     *
     * @param TraversableElement $page     Page element
     * @param string             $username Username
     * @param string             $password Password
     *
     * @return void
     */
    protected function logIn(TraversableElement $page, string $username, string $password = 'password'): void
    {
        $page->clickLink('Log In');
        $this->findCssAndSetValue($page, '#username', $username);
        $this->findCssAndSetValue($page, '#password', $password);
        $this->clickCss($page, '.content input[type="submit"]');
    }

    /**
     * Populate a form and return the first value entered (or empty string if no data provided).
     *
     * @param TraversableElement $page Page containing form
     * @param array              $data Data to enter into the form (indexed by selector)
     *
     * @return string
     */
    protected function populateForm(TraversableElement $page, array $data): string
    {
        $firstValue = null;
        foreach ($data as $selector => $value) {
            $firstValue ??= $value;
            $this->findCssAndSetValue($page, $selector, $value);
        }
        return $firstValue ?? '';
    }

    /**
     * Standard setup method.
     *
     * @return void
     */
    public function setUp(): void
    {
        // Give up if we're not running in CI (throws, so no problem with any
        // further actions in any setUp methods of child classes):
        if (!$this->continuousIntegrationRunning()) {
            $this->markTestSkipped('Continuous integration not running.');
            return;
        }
    }

    /**
     * Standard teardown method.
     *
     * @return void
     */
    public function tearDown(): void
    {
        // Take screenshot of failed test, if we have a screenshot directory set:
        if (
            ($this->status()->isError() || $this->status()->isFailure())
            && ($imageDir = getenv('GBDB_SCREENSHOT_DIR'))
        ) {
            $filename = $this->name() . '-' . hrtime(true);

            // Save HTML snapshot
            $snapshot = $this->getMinkSession()->getPage()->getOuterHtml();
            if (!empty($snapshot)) {
                if (!file_exists($imageDir)) {
                    mkdir($imageDir);
                }

                file_put_contents($imageDir . '/' . $filename . '.html', $snapshot);
            }

            // Save image screenshot
            $imageData = $this->getMinkSession()->getDriver()->getScreenshot();
            if (!empty($imageData)) {
                if (!file_exists($imageDir)) {
                    mkdir($imageDir);
                }

                file_put_contents($imageDir . '/' . $filename . '.png', $imageData);
            }
        }

        $this->stopMinkSession();
    }

    /**
     * Standard tear-down.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        // No teardown actions at this time.
    }
}
