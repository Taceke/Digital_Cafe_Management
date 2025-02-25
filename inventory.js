function fetchInventory() {
    fetch("inventory.php?get_inventory")
        .then(response => response.json())
        .then(data => {
            let materialList = "<h3>Raw Materials</h3><table><tr><th>Name</th><th>Quantity</th><th>Unit</th></tr>";
            data.materials.forEach(material => {
                let rowClass = material.quantity < material.min_stock ? "low-stock" : "";
                materialList += `<tr class="${rowClass}"><td>${material.name}</td><td>${material.quantity}</td><td>${material.unit}</td></tr>`;
            });
            materialList += "</table>";
            document.getElementById("inventory-list").innerHTML = materialList;

            let equipmentList = "<h3>Equipment</h3><table><tr><th>Name</th><th>Quantity</th><th>Condition</th></tr>";
            data.equipment.forEach(equipment => {
                equipmentList += `<tr><td>${equipment.name}</td><td>${equipment.quantity}</td><td>${equipment.item_condition}</td></tr>`;
            });
            equipmentList += "</table>";
            document.getElementById("equipment-list").innerHTML = equipmentList;
        })
        .catch(error => console.error("Error fetching inventory:", error));
}

document.getElementById("material-form").addEventListener("submit", function(event) {
    event.preventDefault();
    let formData = new FormData(this);
    formData.append("add_material", "1");

    fetch("inventory.php", { 
        method: "POST", 
        body: formData 
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        fetchInventory();
        this.reset();
    })
    .catch(error => console.error("Error adding material:", error));
});

document.getElementById("equipment-form").addEventListener("submit", function(event) {
    event.preventDefault();
    let formData = new FormData(this);
    formData.append("add_equipment", "1");

    fetch("inventory.php", { 
        method: "POST", 
        body: formData 
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        fetchInventory();
        this.reset();
    })
    .catch(error => console.error("Error adding equipment:", error));
});

document.addEventListener("DOMContentLoaded", fetchInventory);