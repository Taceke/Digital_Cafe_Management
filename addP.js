document.getElementById("imageUpload").addEventListener("change", function (event) {
    let preview = document.getElementById("previewImage");
    let file = event.target.files[0];
    
    if (file) {
        let reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(file);
    }
});

function addToCart(productId) {
    alert("Product " + productId + " added to cart!");
}
