<?php

namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\Module;
use App\Models\StudentModule;
use App\Models\Result;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class HistogramController extends Controller
{
    public function showHistogram()
    {
        $results = Result::pluck('mark')->toArray();
        return view('histogram', ['results' => $results]);
    }
/*
    public function showStudentAverages()
    {
        $user = Auth::user();
        //dd('   user  :  ' , $user->id);
        //$studentModules = StudentModule::where('student_id', $user->id)->with('modules.results')->get();
        $studentId = Student::where('user_id', $user->id)->value('id');
        $studentModules = StudentModule::where('student_id', $studentId)
            ->with('module') // Charger les modules associés
            ->get();

        //$studentModules = StudentModule::where('student_id', $user->id)->with('module.results')->get();

        //dd(" studentID    :    ", $studentId);
        //dd(" studentModules    :    ", $studentModules);


        $moduleAverages = [];

        $moduleAverages = [];

        foreach ($studentModules as $studentModule) {
            $module = $studentModule->module; //dd("module : " , $module);
            $exams = $module->exams;//dd("module exams : " , $exams);

            foreach ($exams as $exam) {
                 $results = $exam->results; //dd(" exams result : " , $results);

                foreach ($results as $result) {
                     $mark = $result->mark;

                    // ToDo
                }
            }
        }

        //dd($moduleAverages);



        //dd("fuck    :    ", $moduleAverages);

        //dd(" envoyé à la vue : ", $exams);
        return view('student_averages', ['averages' => $exams]);
    }


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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let averagesData = {!! json_encode($averages) !!};

        let labels = averagesData.map(data => data.name);
        let values = averagesData.map(data => {
            let total = data.results.reduce((sum, result) => sum + result.mark, 0);
            return (total / data.results.length).toFixed(2);
        });

        let moduleNames = averagesData.map(data => data.name);
        let moduleColors = [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ];

        let ctx = document.getElementById('histogramChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Moyennes',
                    data: values,
                    backgroundColor: moduleColors,
                    borderColor: moduleColors,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20,
                        title: {
                            display: true,
                            text: 'Notes'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Modules'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Création de la légende avec les noms des modules et les couleurs
        let legend = document.getElementById('legend');
        moduleNames.forEach((name, index) => {
            let colorBox = document.createElement('span');
            colorBox.style.display = 'inline-block';
            colorBox.style.width = '20px';
            colorBox.style.height = '20px';
            colorBox.style.backgroundColor = moduleColors[index];
            colorBox.style.marginRight = '5px';
            legend.appendChild(colorBox);
            legend.appendChild(document.createTextNode(name));
            legend.appendChild(document.createElement('br'));
        });
    </script>

    <div id="legend"></div>
</x-app-layout>


*/

    public function showStudentAverages()
    {
        $user = Auth::user();
        $studentId = Student::where('user_id', $user->id)->value('id');
        $studentModules = StudentModule::where('student_id', $studentId)
            ->with('module.exams.results')
            ->get();

        $moduleAverages = [];

        foreach ($studentModules as $studentModule) {
            $module = $studentModule->module;
            $exams = $module->exams;

            $totalMarks = 0;
            $totalExams = 0;

            foreach ($exams as $exam) {
                $results = $exam->results;

                foreach ($results as $result) {
                    $totalMarks += $result->mark;
                    $totalExams++;
                }
            }

            if ($totalExams > 0) {
                $moduleAverages[] = [
                    'name' => $module->name,
                    'average' => round($totalMarks / $totalExams, 2)
                ];
            }
        }


        return view('student_averages', ['averages' => $moduleAverages]);
    }






}
