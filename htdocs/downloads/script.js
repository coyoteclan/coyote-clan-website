function filterMods(category) {
    const mods = document.querySelectorAll('.mod');
    
    mods.forEach(mod => {
        if (category === 'all' || mod.classList.contains(category)) {
            mod.style.display = 'block';
        } else {
            mod.style.display = 'none';
        }
    });
}

function searchMods() {
    const searchTerm = document.getElementById('mod-search').value.toLowerCase();
    const mods = document.querySelectorAll('.mod');

    for (const mod of mods) {
        const modName = mod.querySelector('h2').textContent.toLowerCase();
        mod.style.display = modName.includes(searchTerm) ? 'block' : 'none';
    }
}

function fetchMods() {
    const orderBy = document.getElementById('order-by').value;
    const sortOrder = document.getElementById('sort-order').value;

    const url = `fetchmods.php?order_by=${orderBy}&sort_order=${sortOrder}`;

    return fetch(url)
        .then(response => response.json())
        .then(data => {
            // Assuming data is an array of mod objects
            displayMods(data);
        })
        .catch(error => console.error('Fetch error:', error));
}

// Assuming you have a function to display mods in the HTML
function displayMods(mods) {
    const modListing = document.getElementById('mod-listing');
    
    // Clear existing content
    modListing.innerHTML = '';

    // Loop through mods and create HTML elements for each mod
    mods.forEach(mod => {
        const modElement = document.createElement('div');
        modElement.className = `mod ${mod.category.toLowerCase()}`; // Set appropriate class based on category

        modElement.innerHTML = `
            <h2>${mod.name}</h2>
            <h6>Author: ${mod.author}</h6>
            <p>${mod.description}</p>
            <img src="${mod.image_url}" class="mod-image">
            <a href="${mod.download_link}">Download</a>
        `;

        // Append the mod element to the modListing div
        modListing.appendChild(modElement);
    });
}

// Call fetchMods when the page loads
document.addEventListener('DOMContentLoaded', fetchMods);

function logout() {
        // You can perform additional cleanup tasks if needed
    window.location.href = "logout.php";
}

function emitPolygons(event) {
    const effectContainer = document.getElementById('effectContainer');
    const colors = ['#bbdc3d', '#b7d83c', '#7855a0', '#d6fa46', '#b882f5'];
    const shapes = [
        'polygon(50% 0%, 100% 100%, 0% 100%)', // Triangle
        'polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)', // Diamond
        'polygon(50% 0%, 100% 25%, 75% 100%, 25% 100%, 50% 25%, 50% 75%)' // Pentagon
    ];

    const clickX = event.clientX;
    const clickY = event.clientY;

    for (let i = 0; i < 6; i++) {
        const polygon = document.createElement('div');
        polygon.classList.add('polygon');
        polygon.style.clipPath = shapes[Math.floor(Math.random() * shapes.length)];
        polygon.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        polygon.style.left = `${clickX}px`;
        polygon.style.top = `${clickY}px`;
        polygon.style.width = `${Math.random() * 30 + 10}px`;
        polygon.style.height = `${Math.random() * 30 + 10}px`;

        const vx = Math.random() * 100 - 50; // Random velocity in x direction (-3 to 3)
        const vy = Math.random() * 100 - 50; // Random velocity in y direction (-3 to 3)
        const rotation = Math.random() * 360; // Random rotation angle

        // Move and rotate the polygon using animation
        polygon.animate(
            [
                { transform: 'translate(0px, 0px) rotate(0deg)' },
                { transform: `translate(${vx}px, ${vy}px) rotate(${rotation}deg)` }
            ],
            {
                duration: 200 + Math.random() * 500, // Duration between 1 and 2 seconds
                easing: 'ease-out',
                fill: 'forwards'
            }
        );

        effectContainer.appendChild(polygon);

        // Remove the polygon after the animation ends
        polygon.addEventListener('animationend', function() {
            polygon.remove();
        });
    }
}

// Attach emitPolygons to all buttons
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('button, a.category-button');
    
    buttons.forEach(button => {
        button.addEventListener('click', (event) => {
            emitPolygons(event); // Pass the event object to emitPolygons
            if (button.getAttribute('onclick')) {
                const existingOnClick = button.getAttribute('onclick');
                if (existingOnClick) {
                    const func = new Function(existingOnClick);
                    func.call(button, event);
                }
            }
        });
    });
});
