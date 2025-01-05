let searchInput = document.getElementById("searchInput");

let produkItemsData = [
    {
        id: "abc",
        name: "Kemeja putih lengan pendek",
        price: 80000,
        img: "gambar/lengan_pndk.jpg",
        sizes: {
            S: 50000,
            M: 60000,
            L: 70000,
            XL: 80000
        }
    },
    {
        id: "def",
        name: "Celana dasar SD panjang",
        price: 70000,
        img: "gambar/celana_pjg.jpg",
        sizes: {
            S: 50000,
            M: 60000,
            L: 70000,
            XL: 80000
        }
    },
    {
        id: "ghi",
        name: "Kemeja putih lengan panjang",
        price: 80000,
        img: "gambar/lengan_pjg.jpg",
        sizes: {
            S: 50000,
            M: 60000,
            L: 70000,
            XL: 80000
        }
    },
    {
        id: "jkl",
        name: "rok dasar SD panjang",
        price: 60000,
        img: "gambar/rok_perempuan.jpeg",
        sizes: {
            S: 50000,
            M: 60000,
            L: 70000,
            XL: 80000
        }
    },
    {
        id: "mno",
        name: "Ikat pinggang",
        price: 25000,
        img: "gambar/ikat_pinggang.jpg",
        sizes: {
            S: 50000,
            M: 60000,
            L: 70000,
            XL: 80000
        }
    },
    {
        id: "pqr",
        name: "Topi SD",
        price: 15000,
        img: "gambar/topi.jpg",
        sizes: {
            S: 50000,
            M: 60000,
            L: 70000,
            XL: 80000
        }
    },
    {
        id: "stu",
        name: "Dasi SD",
        price: 10000,
        img: "gambar/dasi.jpg",
        sizes: {
            S: 50000,
            M: 60000,
            L: 70000,
            XL: 80000
        }
    },
    {
        id: "vwx",
        name: "Kaos Kaki",
        price: 10000,
        img: "gambar/kaos_kaki.jpg",
        sizes: {
            S: 50000,
            M: 60000,
            L: 70000,
            XL: 80000
        }
    },
];

// Fungsi untuk menampilkan produk berdasarkan pencarian
function displayProducts(products) {
    produk.innerHTML = products.map((product) => {
        return `
        <div id = "product-id-${product.id}" class="produk">
        <div class="row">
            <img src="${product.img}">
            <div class="elemen">
                <div class="rating">
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class="bx bxs-star-half"></i>
                </div>
                <div id=${product.id} class="cart-icon">
                    <i onclick="increment(${product.id})" class='bx bx-cart-add'></i>
                </div>
            </div>
            <div class="harga">
                <h4>${product.name}</h4>
                <p> By <span>AStore</span></p>
                <p>Rp ${product.price}</p>
            </div>
            <a href="rincian_produk.html">
                <button class="normal">Detail</button>
            </a>
        </div>
    </div>
        `;
    }).join("");
}

// Fungsi pencarian
function searchProducts() {
    // Ambil nilai input pencarian
    let query = searchInput.value.toLowerCase();

    // Filter produk berdasarkan nama yang mengandung kata pencarian
    let filteredProducts = produkItemsData.filter(product => 
        product.name.toLowerCase().includes(query)
    );

    // Tampilkan produk yang sudah difilter
    displayProducts(filteredProducts);
}

// Panggil fungsi displayProducts saat pertama kali load untuk menampilkan semua produk
displayProducts(produkItemsData);