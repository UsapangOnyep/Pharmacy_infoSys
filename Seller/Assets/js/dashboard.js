document.addEventListener("DOMContentLoaded", function () {
    initDashboard();
});

async function initDashboard() {
    await Promise.all([
        loadIncome(),
        loadTopItem(),
        loadTrendThisMonth(),
        loadLowStockItems(),
        loadNearExpiryItems(),
        loadExpiredItems()
    ]);
}

async function fetchData(url) {
    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error(`Error ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error("Fetch error:", error);
        return null;
    }
}

async function loadIncome() {
    const data = await fetchData('Actions/dashboard/income.php');
    if (data) {
        // convert totalSalesToday to a number and format it as currency
        if (data.totalSalesToday && data.totalSalesThisMonth) {
            const totalSalesThisMonth = parseFloat(data.totalSalesThisMonth);
            const totalSalesToday = parseFloat(data.totalSalesToday);
            if (!isNaN(totalSalesToday)) {
                document.getElementById('incomeToday').textContent = totalSalesToday.toLocaleString('en-PH', {
                    style: 'currency',
                    currency: 'PHP',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
            if (!isNaN(totalSalesThisMonth)) {
                document.getElementById('incomeThisMonth').textContent = totalSalesThisMonth.toLocaleString('en-PH', {
                    style: 'currency',
                    currency: 'PHP',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }   
        } else {
            // Ensure totalSalesToday is defined to avoid errors
            document.getElementById('incomeToday').textContent = data.totalSalesToday || '₱0.00';
            document.getElementById('incomeThisMonth').textContent = data.totalSalesThisMonth || '₱0.00';
        }
    }
}

async function loadTopItem() {
    const data = await fetchData('Actions/dashboard/top-item.php');
    if (data) {
        document.getElementById('topItem').textContent = data.ItemName.toUpperCase() || 'No Data';
        document.getElementById('topItemFooter').innerHTML = "<strong>" + data.QTYSold + "</strong> quantity sold." || 'No Data';
    }
}

async function loadTrendThisMonth() {
    const data = await fetchData('Actions/dashboard/trend-this-month.php');
    const tbody = document.querySelector('#TrendThisMonth tbody');
    tbody.innerHTML = '';

    if (Array.isArray(data) && data.length > 0) {
        data.forEach(item => {
            const row = `
                <tr>
                    <td>${item.ItemName}</td>
                    <td style="text-align:right;">${item.QTYSold}</td>
                    <td></td>
                </tr>`;
            tbody.innerHTML += row;
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="3">--- Nothing follows ---</td> <td></td></tr>';
    }
}


async function loadLowStockItems() {
    const data = await fetchData('Actions/dashboard/low-stock-items.php');
    const tbody = document.querySelector('#lowStockItems tbody');
    tbody.innerHTML = '';

    if (Array.isArray(data) && data.length > 0) {
        data.forEach(item => {
            const row = `
                <tr>
                    <td>${item.ItemName}</td>
                    <td style="text-align:right;">${item.CurStock}</td>
                    <td style="text-align:right;">${item.ReorderLevel}</td>
                    <td></td>
                </tr>`;
            tbody.innerHTML += row;
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="4">--- Nothing follows ---</td> <td></td></tr>';
    }
}

async function loadNearExpiryItems() {
    const data = await fetchData('Actions/dashboard/near-expiry-items.php');
    const tbody = document.querySelector('#nearExpiryItems tbody');
    tbody.innerHTML = '';

    if (Array.isArray(data) && data.length > 0) {
        data.forEach(item => {
            const row = `
                <tr>
                    <td>${item.ItemName}</td>
                    <td style="text-align:center;">${item.ExpiryDate}</td>
                    <td style="text-align:right;">${item.Quantity}</td>
                    <td></td>
                </tr>`;
            tbody.innerHTML += row;
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="2">--- Nothing follows ---</td> <td></td></tr>';
    }
}

async function loadExpiredItems() {
    const data = await fetchData('Actions/dashboard/expired-items.php');
    const tbody = document.querySelector('#expiredItems tbody');
    tbody.innerHTML = '';

    if (Array.isArray(data) && data.length > 0) {
        data.forEach(item => {
            const row = `
                <tr>
                    <td>${item.ItemName}</td>
                    <td style="text-align:center;">${item.ExpiryDate}</td>
                    <td style="text-align:right;">${item.Quantity}</td>
                    <td></td>
                </tr>`;
            tbody.innerHTML += row;
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="2">--- Nothing follows ---</td> <td></td></tr>';
    }
}