@extends('layouts/contentNavbarLayout')
@section('title', 'Dashboard')
@section('vendor-style')
<link rel="stylesheet" href="{{asset('public/assets/vendor/libs/apex-charts/apex-charts.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('public/assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('public/assets/js/dashboards-analytics.js')}}"></script>
@endsection
@section('content')

<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<style>
#chartdiv {
    width: 100%;
    height: 350px;
}
</style>


<h4 class="py-3 mb-1 main-hed">School Management</h4>
<div class="row">
    <div class="col-md-4 col-lg-4 mb-4 order-lg-1 order-2">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-8">
                    <div class="card-body">
                        <h5 class="card-title mb-2 text-nowrap">No of Schools</h5>


                        <h5 class="card-title theme-colo mb-1">8,403</h5>

                        <hr>
                        <a href="javascript:;" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#exampleModal">View Details</a>
                    </div>
                </div>
                <div class="col-4 pt-3 ps-0">
                    <img src="{{asset('public/assets/img/img-3.png')}}" class="rounded-start w-100" alt="View Sales">
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-4 mb-4 order-lg-1 order-2">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-8">
                    <div class="card-body">
                        <h5 class="card-title mb-2 text-nowrap">No of Staffs</h5>


                        <h5 class="card-title theme-colo mb-1">60,000</h5>
                        <hr>

                        <a href="javascript:;" class="btn btn-sm btn-primary">View Details</a>
                    </div>
                </div>
                <div class="col-4 pt-3 ps-0">
                    <img src="{{asset('public/assets/img/img-2.png')}}" class="rounded-start w-100" alt="View Sales">
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-4 col-lg-4 mb-4 order-lg-1 order-2">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-8">
                    <div class="card-body">
                        <h5 class="card-title mb-2 text-nowrap">No of Students</h6>


                            <h5 class="card-title theme-colo mb-1">2,235,362</h5>
                            <hr>
                            <a href="javascript:;" class="btn btn-sm btn-primary">View Details</a>
                    </div>
                </div>
                <div class="col-4 pt-3 ps-0">
                    <img src="{{asset('public/assets/img/img-1.png')}}" class="rounded-start  w-100" alt="View Sales">
                </div>
            </div>
        </div>
    </div>

</div>









<h4 class="py-3 mb-1 main-hed">Project Management</h4>

<div class="row">
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card card-border-shadow-primary h-100 sch-man">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-bowl-hot"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0 info-text">Breakfast</h4>
                </div>
                <hr>
                <div class="row">
                    <div class="col present PA">
                        <p>Present</p>
                        <h3>45</h3>
                    </div>
                    <div class="col present AP">
                        <p>Absent </p>
                        <h3>05</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card card-border-shadow-warning h-100 sch-man">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-warning"><i
                                class="bx bxs-log-in-circle"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0 info-text">FN Class</h4>
                </div>
                <hr>
                <div class="row">
                    <div class="col present PA">
                        <p>Present</p>
                        <h3>45</h3>
                    </div>
                    <div class="col present AP">
                        <p>Absent </p>
                        <h3>05</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card card-border-shadow-danger h-100 sch-man">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-danger"><i class="bx bxs-pizza"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0 info-text">Noon Meal</h4>
                </div>
                <hr>
                <div class="row">
                    <div class="col present PA">
                        <p>Present</p>
                        <h3>45</h3>
                    </div>
                    <div class="col present AP">
                        <p>Absent </p>
                        <h3>05</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card card-border-shadow-info h-100 sch-man">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-info"><i class="bx bxs-log-out-circle"></i></span>
                    </div>
                    <h4 class="ms-1 mb-0 info-text">AN Class </h4>
                </div>
                <hr>
                <div class="row">
                    <div class="col present PA">
                        <p>Present</p>
                        <h3>45</h3>
                    </div>
                    <div class="col present AP">
                        <p>Absent </p>
                        <h3>05</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">School Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tr>
                        <th>Company</th>
                        <th>Contact</th>
                        <th>Country</th>
                    </tr>
                    <tr>
                        <td>Alfreds Futterkiste</td>
                        <td>Maria Anders</td>
                        <td>Germany</td>
                    </tr>
                    <tr>
                        <td>Centro comercial Moctezuma</td>
                        <td>Francisco Chang</td>
                        <td>Mexico</td>
                    </tr>
                    <tr>
                        <td>Ernst Handel</td>
                        <td>Roland Mendel</td>
                        <td>Austria</td>
                    </tr>
                    <tr>
                        <td>Island Trading</td>
                        <td>Helen Bennett</td>
                        <td>UK</td>
                    </tr>
                    <tr>
                        <td>Laughing Bacchus Winecellars</td>
                        <td>Yoshi Tannamuri</td>
                        <td>Canada</td>
                    </tr>
                    <tr>
                        <td>Magazzini Alimentari Riuniti</td>
                        <td>Giovanni Rovelli</td>
                        <td>Italy</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!--<h4 class="py-3 mb-1 main-hed">Zone Wise Details</h4>-->
<!-- HTML -->
<!--<div class="row">-->
<!--    <div class="col-md-12">-->
<!--    <div class="card">-->
<!--     <div class="card-body">-->
<!--    <div id="chartdiv"></div>-->
<!--    </div>-->
<!--    </div>-->
<!--</div>-->
<!--</div>-->

<!-- Chart code -->
<script>
am5.ready(function() {


    // Create root element
    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
    var root = am5.Root.new("chartdiv");


    // Set themes
    // https://www.amcharts.com/docs/v5/concepts/themes/
    root.setThemes([
        am5themes_Animated.new(root)
    ]);


    // Create chart
    // https://www.amcharts.com/docs/v5/charts/xy-chart/
    var chart = root.container.children.push(am5xy.XYChart.new(root, {
        panX: false,
        panY: false,
        wheelX: "panX",
        wheelY: "zoomX",
        paddingLeft: 0,
        layout: root.verticalLayout
    }));


    // Add legend
    // https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
    var legend = chart.children.push(am5.Legend.new(root, {
        centerX: am5.p50,
        x: am5.p50
    }));

    var data = [{
        "year": "2021",
        "europe": 2.5,
        "namerica": 2.5,
        "asia": 2.1,
        "lamerica": 1,
        "meast": 0.8,
        "africa": 0.4
    }, {
        "year": "2022",
        "europe": 2.6,
        "namerica": 2.7,
        "asia": 2.2,
        "lamerica": 0.5,
        "meast": 0.4,
        "africa": 0.3
    }, {
        "year": "2023",
        "europe": 2.8,
        "namerica": 2.9,
        "asia": 2.4,
        "lamerica": 0.3,
        "meast": 0.9,
        "africa": 0.5
    }];


    // Create axes
    // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
    var xRenderer = am5xy.AxisRendererX.new(root, {
        cellStartLocation: 0.1,
        cellEndLocation: 0.9,
        minorGridEnabled: true
    });

    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
        categoryField: "year",
        renderer: xRenderer,
        tooltip: am5.Tooltip.new(root, {})
    }));

    xRenderer.grid.template.setAll({
        location: 1
    })

    xAxis.data.setAll(data);

    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
        min: 0,
        renderer: am5xy.AxisRendererY.new(root, {
            strokeOpacity: 0.1
        })
    }));


    // Add series
    // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
    function makeSeries(name, fieldName, stacked) {
        var series = chart.series.push(am5xy.ColumnSeries.new(root, {
            stacked: stacked,
            name: name,
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: fieldName,
            categoryXField: "year"
        }));

        series.columns.template.setAll({
            tooltipText: "{name}, {categoryX}:{valueY}",
            width: am5.percent(90),
            tooltipY: am5.percent(10)
        });
        series.data.setAll(data);

        // Make stuff animate on load
        // https://www.amcharts.com/docs/v5/concepts/animations/
        series.appear();

        series.bullets.push(function() {
            return am5.Bullet.new(root, {
                locationY: 0.5,
                sprite: am5.Label.new(root, {
                    text: "{valueY}",
                    fill: root.interfaceColors.get("alternativeText"),
                    centerY: am5.percent(50),
                    centerX: am5.percent(50),
                    populateText: true
                })
            });
        });

        legend.data.push(series);
    }

    makeSeries("Europe", "europe", false);
    makeSeries("North America", "namerica", true);
    makeSeries("Asia", "asia", false);
    makeSeries("Latin America", "lamerica", true);
    makeSeries("Middle East", "meast", true);
    makeSeries("Africa", "africa", true);


    // Make stuff animate on load
    // https://www.amcharts.com/docs/v5/concepts/animations/
    chart.appear(1000, 100);

}); // end am5.ready()
</script>

@endsection






// document.addEventListener('DOMContentLoaded', function() {
// document.getElementById('viewSchoolDetails').addEventListener('click', function() {
// fetch('{{ route('
//                 school.details ') }}')
// .then(response => response.json())
// .then(data => {
// const schoolDetailsBody = document.getElementById('schoolDetailsBody');
// schoolDetailsBody.innerHTML = '';
// data.forEach((school, index) => {
// const zoneName = zoneMap[school.zone_name] || school.zone_name;
// const talukName = talukMap[school.taluk_name] || school.taluk_name;
// const district_name = districtMap[school.district_name] || school
// .district_name;
// console.log(district_name, talukName, zoneName);
// const row = document.createElement('tr');
// row.innerHTML = `
// <td>${index + 1}</td>
// <td>${zoneName}</td>
// <td>${district_name}</td>
// <td>${talukName}</td>
// <td>${school.school_name}</td>
// `;
// schoolDetailsBody.appendChild(row);
// });
// })
// .catch(error => console.error('Error fetching school details:', error));
// });



// document.getElementById('viewstaffdetails').addEventListener('click', function() {
// fetch('{{ route('staff.details') }}')
// .then(response => {
// if (!response.ok) {
// throw new Error('Network response was not ok');
// }
// return response.json();
// })
// .then(data => {
// console.log('Received data:', data);
// const staffDetailsBody = document.getElementById('staffDetailsBody');
// staffDetailsBody.innerHTML = '';
// if (Array.isArray(data)) {
// console.log(data);
// data.forEach((staff, index) => {
// const row = document.createElement('tr');
// row.innerHTML = `
// <td>${index + 1}</td>
// <td>${staff.emp_name}</td>
// <td>${staff.designation}</td>
// <td>${staff.school}</td>
// `;
// staffDetailsBody.appendChild(row);
// });
// } else {
// console.error('Unexpected response format:', data);
// alert('An unexpected error occurred. Please try again.');
// }
// })
// .catch(error => {
// console.error('Error fetching staff details:', error);
// alert('An error occurred while fetching staff details. Please try again later.');
// });
// });

// });