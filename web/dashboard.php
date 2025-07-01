<!doctype html>
<?php
    $pageTitle = 'Ticket Metrics Dashboard';
    include('templates/header.php');
?>

<script src="libraries/chartjs/chart.js"></script>
<!-- <link rel="stylesheet" href="css/dashboard.css"> -->

<style> 
    .chart-container {
        width: 100%;
        max-width: 500px;
        margin: 20px auto;
    }

    .chart-title {
        text-align: center;
        margin-bottom: 10px;
    }
    .dashboard-container {
        max-width: 800px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
    }

    .table_component {
    overflow: auto;
    width: 100%;
    }

    .table_component table {
        border: 1px solid #dededf;
        height: 100%;
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
        border-spacing: 1px;
        text-align: left;
    }

    .table_component caption {
        caption-side: top;
        text-align: left;
    }

    .table_component th {
        border: 1px solid #dededf;
        background-color: #eceff1;
        color: #000000;
        padding: 8px;
        box-sizing: border-box;
    }

    .table_component td {
        border: 1px solid #dededf;
        background-color: #ffffff;
        color: #000000;
        padding: 8px;
        box-sizing: border-box;
    }
    td:nth-child(1), th:nth-child(1) { /* First column */
    width: 150px;
    }
    td:nth-child(2), th:nth-child(2) { /* Second column */
        width: 600px;
    }
    td:nth-child(3), th:nth-child(3) { /* Third column */
        width: 150px;
    }



</style>

</head>

<body>

<?php 
include('templates/menu.php');
?> 
    
    <div class="header">
        
        <h2 style="text-align: center;">Metricas de Ordenes de Trabajo</h2>
        <!-- <a href="index.php" class="logout">Logout</a> -->
    </div>

    <!-- <h1>Metricas Dashboard</h1> -->

    <div class="dashboard-container">
        
        <div class="table_component" role="region" tabindex="0">
            <table>
                <caption></caption>
                    <thead>
                        <tr>
                            <th>Metrica por</th>
                            <th>Grafico</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>Tickets by Status</td>
                            <td>
                                <div class="chart-container">
                                <h2 class="chart-title">Tickets by Status</h2>
                                <canvas id="ticketsByStatusChart"></canvas>
                            </td>
                        </tr>
                        <tr>
                            <td>Tickets by User</td>
                            <td>
                                <div class="chart-container">
                                    <h2 class="chart-title">Tickets by User</h2>
                                    <canvas id="ticketsByUserChart"></canvas>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Tickets by Customer</td>
                            <td>        
                                <div class="chart-container">
                                <h2 class="chart-title">Tickets by Customer</h2>
                                <canvas id="ticketsByCustomerChart"></canvas>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Tickets by Site</td>
                            <td>        
                                <div class="chart-container">
                                <h2 class="chart-title">Tickets by Site</h2>
                                <canvas id="ticketsBySiteChart"></canvas>
                                </div>
                            </td>
                        </tr>
                    </tbody>

            </table>

        <!-- CIERRE table_component -->
        </div>

    <!-- CIERRE DASHBOARD CONTAINER -->
    </div>
      


    <script>
        //  --- Chart.js code from previous responses goes here ---

        // Tickets by Status Chart
        fetch('stats_status.php')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('ticketsByStatusChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Tickets by Status',
                        data: data.datasets[0].data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        // Tickets by User Chart
        // fetch('stats_user.php')
        // .then(response => response.json())
        // .then(data => {
        //     const ctx = document.getElementById('ticketsByUserChart').getContext('2d');
        //     const myChart = new Chart(ctx, {
        //         type: 'bar',
        //         data: {
        //             labels: data.labels,
        //             datasets: [{
        //                 label: 'Tickets by User',
        //                 data: data.datasets[0].data,
        //                 backgroundColor: [
        //                     'rgba(255, 99, 132, 0.2)',
        //                     'rgba(54, 162, 235, 0.2)',
        //                     'rgba(255, 206, 86, 0.2)',
        //                     'rgba(75, 192, 192, 0.2)',
        //                     'rgba(153, 102, 255, 0.2)',
        //                     'rgba(255, 159, 64, 0.2)'
        //                 ],
        //                 borderColor: [
        //                     'rgba(255, 99, 132, 1)',
        //                     'rgba(54, 162, 235, 1)',
        //                     'rgba(255, 206, 86, 1)',
        //                     'rgba(75, 192, 192, 1)',
        //                     'rgba(153, 102, 255, 1)',
        //                     'rgba(255, 159, 64, 1)'
        //                 ],
        //                 borderWidth: 1
        //             }]
        //         },
        //         options: {
        //             scales: {
        //                 y: {
        //                     beginAtZero: true
        //                 }
        //             }
        //         }
        //     });
        // });


        //stats_user con PIE chart
            // fetch('stats_user_pie.php')
            // .then(response => response.json())
            // .then(data => {
            //     const ctx = document.getElementById('ticketsByUserChart').getContext('2d');
            //     const myChart = new Chart(ctx, {
            //         type: 'pie', // Change the chart type to 'pie'
            //         data: {
            //             labels: data.labels,
            //             datasets: [{
            //                 label: 'Tickets by User',
            //                 data: data.datasets[0].data,
            //                 backgroundColor: [
            //                     'rgba(255, 99, 132, 0.6)',
            //                     'rgba(54, 162, 235, 0.6)',
            //                     'rgba(255, 206, 86, 0.6)',
            //                     'rgba(75, 192, 192, 0.6)',
            //                     'rgba(153, 102, 255, 0.6)',
            //                     'rgba(255, 159, 64, 0.6)'
            //                 ],
            //                 borderColor: [
            //                     'rgba(255, 99, 132, 1)',
            //                     'rgba(54, 162, 235, 1)',
            //                     'rgba(255, 206, 86, 1)',
            //                     'rgba(75, 192, 192, 1)',
            //                     'rgba(153, 102, 255, 1)',
            //                     'rgba(255, 159, 64, 1)'
            //                 ],
            //                 borderWidth: 1
            //             }]
            //         },
            //         options: {
            //             responsive: true,
            //             plugins: {
            //                 legend: {
            //                     position: 'top',
            //                 },
            //                 title: {
            //                     display: true,
            //                     text: 'Tickets by User',
            //                     padding: 10
            //                 }
            //             }
            //         }
            //     });
            // });
 

            // stats_user con doughnut chart
            fetch('stats_user_pie.php')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('ticketsByUserChart').getContext('2d');
                    const myChart = new Chart(ctx, {
                        type: 'doughnut', // Change the chart type to 'doughnut'
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Tickets by User',
                                data: data.datasets[0].data,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.6)',
                                    'rgba(54, 162, 235, 0.6)',
                                    'rgba(255, 206, 86, 0.6)',
                                    'rgba(75, 192, 192, 0.6)',
                                    'rgba(153, 102, 255, 0.6)',
                                    'rgba(255, 159, 64, 0.6)'
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Tickets by User',
                                    padding: 10
                                }
                            },
                            cutout: '50%' // Adjust the size of the hole in the doughnut chart
                        }
                    });
                });


        // Tickets by Customer Chart
        fetch('stats_customer.php')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('ticketsByCustomerChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Tickets by Customer',
                        data: data.datasets[0].data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        // Tickets by Site Chart
        fetch('stats_site.php')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('ticketsBySiteChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Tickets by Site',
                        data: data.datasets[0].data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

<?php
    include('templates/footer.php');
?>