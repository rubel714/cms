
// Declare modal variable for each modal
var groupModal = document.getElementById("groupModal");
var cetegoryModal= document.getElementById("cetegoryModal");
var genericModal = document.getElementById("genericModal");
var manufacturerModal = document.getElementById("manufacturerModal");
var supplierModal = document.getElementById("supplierModal");
var customerModal = document.getElementById("customerModal");
var strengthModal = document.getElementById("strengthModal");
var productModal = document.getElementById("productModal");
 

// Declare Modal Opn Function
function openModal(modal) {
    modal.style.display = "block";
    document.querySelector(".btnUpdate").style.display = "none";
}

// Declare Modal close Function
function closeModal(modal) {
    modal.style.display = "none";
}

// Declare Modal Update Function
function updateModal(modal) {
    modal.style.display = "block";
    document.modal.querySelector(".btnSave").style.display = "none";
}

// Window close for Group Modal
window.onclick = function (event) {
    if (event.target == groupModal ) {
        groupModal.style.display = "none";
    }

    else if(event.target == cetegoryModal){
        cetegoryModal.style.display = "none"; 
    }

    else if (event.target == genericModal) {
        genericModal.style.display = "none";
    }

    else if (event.target == manufacturerModal) {
        manufacturerModal.style.display = "none";
    }

    else if (event.target == supplierModal) {
        supplierModal.style.display = "none";
    }

    else if (event.target == customerModal) {
        customerModal.style.display = "none";
    }

    else if (event.target == strengthModal) {
        strengthModal.style.display = "none";
    }

    else if (event.target == productModal) {
        productModal.style.display = "none";
    }
}
