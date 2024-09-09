<x-app-layout>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <canvas id="histogramChart" width="200" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        let moduleData = {!! json_encode($averages) !!};

        let labels = moduleData.map(data => data.name);
        let values = moduleData.map(data => data.average);

        let ctx = document.getElementById('histogramChart').getContext('2d');

        let colors = [
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)',
            'rgba(153, 102, 255, 0.6)',
            'rgba(255, 159, 64, 0.6)'
        ];

        let legends = moduleData.map((data, index) => {
            return {
                text: `${data.name}`,
                fillStyle: colors[index]
            };
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Modules',
                    data: values,
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        title: {
                            display: true,
                            text: 'Notes'
                        },
                        beginAtZero: true,
                        max: 20
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Module'
                        },
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Notes étudiant par module'
                },
                legend: {
                    display: false
                }
            }
        });

        legends.forEach((legend, index) => {
            let legendDiv = document.createElement('div');
            legendDiv.innerHTML = `
                <span style="background-color: ${legend.fillStyle}; width: 12px; height: 12px; display: inline-block;"></span>
                <span>${legend.text}</span>
            `;
            document.getElementById('legend').appendChild(legendDiv);
        });
    </script>

    <div id="legend" class="flex items-center space-x-2"></div>

</x-app-layout>
