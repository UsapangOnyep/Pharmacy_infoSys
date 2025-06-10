function formatToPeso(value) {
  return "₱" + Number(value).toLocaleString("en-PH", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

async function loadTrendChart() {
  try {
    const response = await fetch("Actions/dashboard/trend-this-month.php");
    const data = await response.json();

    // Extract labels (ItemName) and data points (QTYSold)
    const labels = data.map((item) => item.ItemName);
    const quantities = data.map((item) => parseInt(item.QTYSold));

    const ctx = document.getElementById("trendChart").getContext("2d");

    // Destroy existing chart instance if exists to avoid duplicates
    if (window.trendChartInstance) {
      window.trendChartInstance.destroy();
    }

    const config = {
      type: "pie",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Top 5 Items Sold This Month",
            data: quantities,
            backgroundColor: [
              "#FF6384",
              "#36A2EB",
              "#FFCE56",
              "#4BC0C0",
              "#9966FF",
            ],
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false, // Important for controlling height
        plugins: {
          legend: {
            position: "right",
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                return context.formattedValue;
              },
            },
          },
        },
      },
    };

    // Now create the chart
    window.trendChartInstance = new Chart(ctx, config);
  } catch (error) {
    console.error("Error loading trend data:", error);
  }
}

async function loadMonthlySalesChart() {
  try {
    const response = await fetch("Actions/Sales/sales-Summary.php");
    const data = await response.json();

    const labels = [
      "January",
      "February",
      "March",
      "April",
      "May",
      "June",
      "July",
      "August",
      "September",
      "October",
      "November",
      "December",
    ];

    // Populate data in order from month 1 to 12
    const monthlySales = [];
    for (let i = 1; i <= 12; i++) {
      monthlySales.push(data[i] || 0);
    }

    const ctx = document.getElementById("salesSummary").getContext("2d");

    if (window.salesSummaryInstance) {
      window.salesSummaryInstance.destroy();
    }

    const config = {
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Monthly Sales " + new Date().getFullYear(),
            data: monthlySales,
            backgroundColor: "rgba(54, 162, 235, 0.7)",
            borderColor: "rgba(54, 162, 235, 1)",
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
          },
        },
        plugins: {
          legend: {
            position: "top",
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                return formatToPeso(context.raw);
              },
            },
          },
        },
      },
    };

    window.salesSummaryInstance = new Chart(ctx, config);
  } catch (error) {
    console.error("Error loading monthly sales chart:", error);
  }
}

async function loadSevenDaysSalesChart() {
  try {
    const response = await fetch("Actions/Sales/seven-days-summary.php");
    const data = await response.json();

    const labels = Object.keys(data); // e.g., ["2025-06-04", "2025-06-05", ...]
    const values = Object.values(data); // corresponding sales amounts

    const ctx = document.getElementById("last7DaysSalesChart").getContext("2d");

    if (window.last7DaysSalesChartInstance) {
      window.last7DaysSalesChartInstance.destroy();
    }

    const config = {
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Sales (Last 7 Days)",
            data: values,
            backgroundColor: "rgba(75, 192, 192, 0.7)",
            borderColor: "rgba(75, 192, 192, 1)",
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "top",
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                return formatToPeso(context.raw);
              },
            },
          },
        },
      },
    };

    window.last7DaysSalesChartInstance = new Chart(ctx, config);
  } catch (error) {
    console.error("Error loading 7-day sales chart:", error);
  }
}

// Load chart on page ready
document.addEventListener("DOMContentLoaded", () => {
  loadTrendChart();
  loadMonthlySalesChart();
  loadSevenDaysSalesChart();
});