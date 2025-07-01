document.addEventListener('DOMContentLoaded', function() {
    // Get references to the select elements
    const customerSelect = document.getElementById('customer_id');
    const siteSelect = document.getElementById('site_id');
    
    // Add event listener to customer select
    customerSelect.addEventListener('change', function() {
        const customerId = this.value;
        
        // Clear the site select
        siteSelect.innerHTML = '<option value="">Select Site</option>';
        
        // If no customer is selected, do nothing else
        if (!customerId) {
            return;
        }
        
        // Fetch sites for the selected customer
        fetch(`get_sites.php?customer_id=${customerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                    return;
                }
                
                // Add site options to the select element
                data.forEach(site => {
                    const option = document.createElement('option');
                    option.value = site.id;
                    option.textContent = site.name;
                    siteSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
    });
});