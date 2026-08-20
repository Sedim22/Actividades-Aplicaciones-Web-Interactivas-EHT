const hero_images = [
    'assets/images/hero/example.png',
    'assets/images/hero/example2.jpg',
    'assets/images/hero/example3.jpg',
];

let hero_image = 0;

function changeHeroImage(){
    const hero_image_div = document.getElementById("hero-background-image");
    if(!hero_image_div){
        console.error("ERROR:: Could not find hero image div");
        return;
    }

    hero_image_div.style.opacity = 0;

    setTimeout(() => {
        hero_image = (hero_image + 1) % hero_images.length;
        hero_image_div.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("${hero_images[hero_image]}")`;
        hero_image_div.style.opacity = 1;
        
    }, 500);
}

setInterval(changeHeroImage,5000);

class ProductComponent extends HTMLElement{
    constructor(){
        super();
        this.shadow = this.attachShadow({mode:'open'});
    }

    connectedCallback(){
        this._title = this.getAttribute('title') || 'Producto sin título';
        this._image = this.getAttribute('image') || 'default.png';
        this._price = this.getAttribute('price') || '$0.00';
        this.render();
    }

    render(){
        this.shadow.innerHTML = `
                <link rel="stylesheet" href="style.css">
                <div class="featured-product">
                    <img src="${this._image}" alt="product image">
                    <div class="product-title"><span>${this._title}</span></div>
                    <div class="product-price"><span>${this._price}</span></div>
                    <button>BUY</button>
                </div>
        `;
    }
}

customElements.define('product-component',ProductComponent);

function addProduct({title,price,image}){
    const container = document.getElementById("featured-products");
    if(!container){
        console.error("ERROR:: Could not find hero image div");
        return;
    }

    const product = document.createElement('product-component');
    product.setAttribute('title',title || "unkown product");
    product.setAttribute('image',image || "");
    product.setAttribute('price',`$${price || 0} USD`);

    container.appendChild(product);
}

async function loadProducts(jproducts){
    
    
    const container = document.getElementById("featured-products");

    if(!container){
        console.error("could not find product container");
        return;
    }

    container.replaceChildren();
    
    const response = await fetch(jproducts);
    const products = await response.json();

    try{
        products.forEach(product => {
            console.log(product);
            addProduct(product);
        });
    }catch(error){
        console.error("Unable to open json products ", error);
    }

}

loadProducts("arduino-products.json");