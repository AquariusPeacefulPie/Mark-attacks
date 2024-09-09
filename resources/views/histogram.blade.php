<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <canvas id="histogramChart" width="200" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('histogramChart').getContext('2d');
        var results = {!! json_encode($results) !!};

        var data = {
            labels: Array.from({length: 21}, (_, i) => i.toString()),
            datasets: [{
                label: 'Fréquence des notes par étudiant',
                data: Array.from({length: 21}, (_, i) => results.filter(result => result === i).length),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        var options = {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Notes'
                    },
                    beginAtZero: true
                },
                y: {
                    title: {
                        display: true,
                        text: 'Fréquence'
                    },
                    beginAtZero: true,
                    max: Math.max(...data.datasets[0].data) + 1
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Fréquences de notes par étudiant'
                },
                legend: {
                    display: false
                }
            }
        };

        var chart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: options
        });
    });
</script>

</x-app-layout>
