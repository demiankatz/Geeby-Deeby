/*global basePath */
function setupSearch() {
    document.querySelectorAll('.search-controls').forEach((element) => {
        element.classList.remove('hidden');
    });
    document.querySelectorAll('.creator-toggle').forEach((element) => {
        let creatorsVisible = sessionStorage.getItem("creators_visible") === "1";
        let creatorsLoading = false;
        const loadCreators = async function () {
            if (!creatorsLoading) {
                creatorsLoading = true;
                const ids = [];
                document.querySelectorAll('.creators').forEach((element) => {
                    ids.push(element.dataset.itemId);
                });
                const uniqueIds = [...new Set(ids)];
                let formData = new FormData();
                formData.append('ids', uniqueIds);
                const response = await fetch(basePath + "/Search/CreatorAjax", { body: formData, method: "post" });
                const json = await response.json();
                for (const id in json.msg) {
                    const text = json.msg[id];
                    document.querySelectorAll('.creators[data-item-id="' + id + '"]').forEach((element) => {
                        element.dataset.loaded = "1";
                        element.classList.remove("hidden");
                        element.querySelector('.values').innerHTML = text;
                    });
                };
            }
        };
        if (creatorsVisible) {
            loadCreators();
            element.checked = true;
        }
        element.addEventListener('change', (event) => {
            creatorsVisible = element.checked;
            sessionStorage.setItem("creators_visible", creatorsVisible ? "1" : "0");
            if (creatorsVisible) {
                document.querySelectorAll('.creators[data-loaded="1"]').forEach((element) => {
                    element.classList.remove('hidden');
                });
                loadCreators();
            } else {
                document.querySelectorAll('.creators').forEach((element) => {
                    element.classList.add('hidden');
                });
            }
        });
    });
    document.querySelectorAll('.edition-attribute-toggle').forEach((element) => {
        const attributeId = element.dataset.editionAttributeId;
        const storageKey = "edition_attribute_" + attributeId + "_visible";
        let attributesVisible = sessionStorage.getItem(storageKey) === "1";
        let attributesLoading = false;
        const loadAttributes = async function () {
            if (!attributesLoading) {
                attributesLoading = true;
                const ids = [];
                document.querySelectorAll('.edition-attributes').forEach((element) => {
                    ids.push(element.dataset.itemId);
                });
                const uniqueIds = [...new Set(ids)];
                let formData = new FormData();
                formData.append('attribute_id', attributeId);
                formData.append('ids', uniqueIds);
                const response = await fetch(basePath + "/Search/EditionAttributeAjax", { body: formData, method: "post" });
                const json = await response.json();
                for (const id in json.msg) {
                    const text = json.msg[id];
                    document.querySelectorAll('.edition-attributes[data-edition-attribute-id="' + attributeId + '"][data-item-id="' + id + '"]').forEach((element) => {
                        element.dataset.loaded = "1";
                        element.classList.remove("hidden");
                        element.querySelector('.values').innerHTML = text;
                    });
                };
            }
        };
        if (attributesVisible) {
            loadAttributes();
            element.checked = true;
        }
        element.addEventListener('change', (event) => {
            attributesVisible = element.checked;
            sessionStorage.setItem(storageKey, attributesVisible ? "1" : "0");
            if (attributesVisible) {
                document.querySelectorAll('.edition-attributes[data-edition-attribute-id="' + attributeId + '"][data-loaded="1"]').forEach((element) => {
                    element.classList.remove('hidden');
                });
                loadAttributes();
            } else {
                document.querySelectorAll('.edition-attributes[data-edition-attribute-id="' + attributeId + '"]').forEach((element) => {
                    element.classList.add('hidden');
                });
            }
        });
    });
}

$('document').ready(setupSearch);