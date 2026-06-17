<x-app-layout :title="__('Dashboard')">
    <x-page-header titre="Tableau de bord" :sous-titre="__('Bienvenue, :name ! Voici un aperçu de l\'activité fiscale.', ['name' => auth()->user()->name])" />

    {{-- Recouvrements des 12 derniers mois (collectivité connectée) --}}
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card rounded-3 overflow-hidden h-100 mb-3">
                <div class="card-body bg-line-chart-gradient d-flex flex-column justify-content-between">
                    <div class="row align-items-center g-0" data-bs-theme="light">
                        <div class="col">
                            <h4 class="text-white mb-0">Recouvrements ce mois-ci · {{ number_format($recouvrements['mois_courant'], 0, ',', ' ') }} FCFA</h4>
                            <p class="fs-10 fw-semi-bold text-white mb-0">Mois précédent <span class="opacity-50">{{ number_format($recouvrements['mois_precedent'], 0, ',', ' ') }} FCFA</span></p>
                        </div>
                        <div class="col-auto d-none d-sm-block">
                            <span class="badge badge-subtle-light">12 derniers mois · Total {{ number_format($recouvrements['total'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                    <div class="echart-recouvrement-12-mois mt-3" style="height:240px"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Indicateurs clés (KPI) de l'exercice fiscal en cours — collectivité connectée --}}
    <div class="row g-3 mb-3" id="statBloc1">
        <div class="col-sm-6 col-md-3">
            <div class="card overflow-hidden" style="min-width: 12rem">
                <div class="bg-holder bg-card" style="background-image:url({{ asset('assets/img/icons/spot-illustrations/corner-1.png') }});"></div>
                <!--/.bg-holder-->
                <div class="card-body position-relative">
                    <h6>Contribuables actifs<span class="badge badge-subtle-warning rounded-pill ms-2">{{ number_format($indicateurs['etablissements_actifs'], 0, ',', ' ') }} étab.</span></h6>
                    <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif text-warning">{{ number_format($indicateurs['contribuables_actifs'], 0, ',', ' ') }}</div>
                    <a class="fw-semi-bold fs-10 text-nowrap" href="{{ route('contribuables.index') }}">Voir le recensement<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card overflow-hidden" style="min-width: 12rem">
                <div class="bg-holder bg-card" style="background-image:url({{ asset('assets/img/icons/spot-illustrations/corner-2.png') }});"></div>
                <!--/.bg-holder-->
                <div class="card-body position-relative">
                    <h6>Montant émis @if($indicateurs['exercice_annee'])<span class="badge badge-subtle-info rounded-pill ms-2">Exercice {{ $indicateurs['exercice_annee'] }}</span>@endif</h6>
                    <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif text-info">{{ number_format($indicateurs['montant_emis'], 0, ',', ' ') }} <span class="fs-10 text-500">FCFA</span></div>
                    <a class="fw-semi-bold fs-10 text-nowrap" href="{{ route('emissions.index') }}">{{ number_format($indicateurs['nb_emissions'], 0, ',', ' ') }} émission(s)<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card overflow-hidden" style="min-width: 12rem">
                <div class="bg-holder bg-card" style="background-image:url({{ asset('assets/img/icons/spot-illustrations/corner-3.png') }});"></div>
                <!--/.bg-holder-->
                <div class="card-body position-relative">
                    <h6>Recouvré<span class="badge badge-subtle-success rounded-pill ms-2">{{ number_format($indicateurs['taux_recouvrement'], 1, ',', ' ') }} %</span></h6>
                    <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif text-success">{{ number_format($indicateurs['montant_recouvre'], 0, ',', ' ') }} <span class="fs-10 text-500">FCFA</span></div>
                    <a class="fw-semi-bold fs-10 text-nowrap" href="{{ route('recouvrements.index') }}">Voir les recouvrements<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card overflow-hidden" style="min-width: 12rem">
                <div class="bg-holder bg-card" style="background-image:url({{ asset('assets/img/icons/spot-illustrations/corner-4.png') }});"></div>
                <!--/.bg-holder-->
                <div class="card-body position-relative">
                    <h6>Reste à recouvrer<span class="badge badge-subtle-danger rounded-pill ms-2">{{ number_format(max(100 - $indicateurs['taux_recouvrement'], 0), 1, ',', ' ') }} %</span></h6>
                    <div class="display-4 fs-5 mb-2 fw-normal font-sans-serif text-danger">{{ number_format($indicateurs['reste_a_recouvrer'], 0, ',', ' ') }} <span class="fs-10 text-500">FCFA</span></div>
                    <a class="fw-semi-bold fs-10 text-nowrap" href="{{ route('emissions.index') }}">Émissions à solder<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                </div>
            </div>
        </div>
    </div>

    {{-- Répartitions analytiques de l'exercice en cours (cartes à mini-graphique) --}}
    <div class="row g-3 mb-3">
        {{-- Objectif de recouvrement : réalisé vs cible (jauge) --}}
        <div class="col-md-6">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2 d-flex align-items-center">Objectif de recouvrement<span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Recouvré sur l'exercice rapporté à l'objectif annuel"><span class="far fa-question-circle" data-fa-transform="shrink-1"></span></span></h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-between p-0">
                    <div class="echart-kpi-objectif w-100" style="height:230px" data-taux="{{ $repartitions['objectif']['taux'] }}"></div>
                    <p class="text-center fs-10 mb-2 px-3">
                        <span class="text-700 fw-semi-bold">{{ number_format($repartitions['objectif']['recouvre'], 0, ',', ' ') }}</span>
                        <span class="text-500"> / {{ number_format($repartitions['objectif']['montant'], 0, ',', ' ') }} FCFA</span>
                    </p>
                </div>
                @include('partials.dashboard-footer-exercice')
            </div>
        </div>
        {{-- Montant émis par nature de taxe (barres) --}}
        <div class="col-md-6">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Émissions par nature</h6>
                </div>
                <div class="card-body d-flex align-items-center p-2">
                    @if (count($repartitions['natures_taxe']['labels']))
                        <div class="echart-kpi-natures w-100" style="height:240px"></div>
                    @else
                        <p class="text-500 fs-10 mb-0 w-100 text-center">Aucune émission sur l'exercice</p>
                    @endif
                </div>
                @include('partials.dashboard-footer-exercice')
            </div>
        </div>
        {{-- Recouvrements par mode de règlement (anneau) --}}
        <div class="col-md-6">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Modes de règlement</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center p-2">
                    @if (count($repartitions['modes_reglement']['labels']))
                        <div class="echart-pie-modes-align w-100" style="min-height:260px"></div>
                    @else
                        <p class="text-500 fs-10 mb-0 text-center">Aucun encaissement sur l'exercice</p>
                    @endif
                </div>
                @include('partials.dashboard-footer-exercice')
            </div>
        </div>
        {{-- Structure des contribuables PP / PM (anneau) --}}
        <div class="col-md-6">
            <div class="card h-md-100">
                <div class="card-header pb-0">
                    <h6 class="mb-0 mt-2">Contribuables PP / PM</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center p-2">
                    @if ($repartitions['personnes']['physiques'] + $repartitions['personnes']['morales'] > 0)
                        <div class="echart-kpi-personnes w-100" style="height:240px"></div>
                    @else
                        <p class="text-500 fs-10 mb-0 text-center">Aucun contribuable actif</p>
                    @endif
                </div>
                @include('partials.dashboard-footer-exercice')
            </div>
        </div>
    </div>

    <div class="row g-0">
        <div class="col-lg-6 pe-lg-2 mb-3">
            <div class="card h-lg-100 overflow-hidden">
                <div class="card-header bg-body-tertiary">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="mb-0">Top 5 des contribuables</h6>
                            <p class="fs-11 text-500 mb-0">Classement sur le total recouvré (toutes taxes et établissements)</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @php
                        $couleursTop = ['primary', 'success', 'info', 'warning', 'danger'];
                    @endphp
                    @forelse ($topContribuables as $index => $contribuable)
                        @php $couleur = $couleursTop[$index % count($couleursTop)]; @endphp
                        <div class="row g-0 align-items-center py-2 position-relative {{ $loop->last ? '' : 'border-bottom' }} border-200">
                            <div class="col ps-x1 py-1 position-static">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xl me-3">
                                        <div class="avatar-name rounded-circle bg-{{ $couleur }}-subtle text-dark"><span class="fs-9 text-{{ $couleur }}">{{ $contribuable['initiale'] }}</span></div>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="{{ route('contribuables.show', $contribuable['contribuable_id']) }}">{{ \Illuminate\Support\Str::limit($contribuable['nom_affiche'], 28) }}</a><span class="badge rounded-pill ms-2 bg-200 text-primary">#{{ $index + 1 }}</span></h6>
                                        <p class="fs-11 text-500 mb-0">{{ $contribuable['numero'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col py-1">
                                <div class="row flex-end-center g-0">
                                    <div class="col-auto pe-2">
                                        <div class="fs-10 fw-semi-bold">{{ number_format($contribuable['total'], 0, ',', ' ') }} FCFA</div>
                                    </div>
                                    <div class="col-5 pe-x1 ps-2">
                                        <div class="progress bg-200 me-2" style="height: 5px;" role="progressbar" aria-valuenow="{{ $contribuable['pourcentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-{{ $couleur }} rounded-pill" style="width: {{ $contribuable['pourcentage'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-500 fs-10 text-center py-5 mb-0">Aucun recouvrement enregistré</p>
                    @endforelse
                </div>
                <div class="card-footer bg-body-tertiary p-0"><a class="btn btn-sm btn-link d-block w-100 py-2" href="{{ route('contribuables.index') }}">Voir tous les contribuables<span class="fas fa-chevron-right ms-1 fs-11"></span></a></div>
            </div>
        </div>
        <div class="col-lg-6 ps-lg-2 mb-3">
            <div class="card h-lg-100">
                <div class="card-header">
                    <div class="row flex-between-center">
                        <div class="col-auto">
                            <h6 class="mb-0">Émissions des 12 derniers mois</h6>
                            <p class="fs-11 text-500 mb-0">Total émis sur la période · {{ number_format($emissions['total'], 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="col-auto">
                            <span class="badge badge-subtle-primary rounded-pill">Ce mois {{ number_format($emissions['mois_courant'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                </div>
                <div class="card-body h-100 pe-0">
                    <div class="echart-emissions-12-mois h-100" style="min-height:240px" data-echart-responsive="true"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                var el = document.querySelector('.echart-recouvrement-12-mois');
                if (!el || typeof echarts === 'undefined') return;

                var labels = @json($recouvrements['labels']);
                var montants = @json($recouvrements['montants']);
                var fmt = new Intl.NumberFormat('fr-FR');

                var chart = echarts.init(el);
                chart.setOption({
                    tooltip: {
                        trigger: 'axis',
                        formatter: function (params) {
                            var p = params[0];
                            return p.axisValue + '<br/><strong>' + fmt.format(p.data) + ' FCFA</strong>';
                        }
                    },
                    grid: { left: '2%', right: '3%', top: '12%', bottom: '5%', containLabel: true },
                    xAxis: {
                        type: 'category',
                        data: labels,
                        boundaryGap: false,
                        axisLine: { lineStyle: { color: 'rgba(255,255,255,0.3)' } },
                        axisTick: { show: false },
                        axisLabel: { color: 'rgba(255,255,255,0.85)', fontSize: 11 }
                    },
                    yAxis: {
                        type: 'value',
                        splitLine: { lineStyle: { color: 'rgba(255,255,255,0.15)' } },
                        axisLabel: {
                            color: 'rgba(255,255,255,0.85)', fontSize: 11,
                            formatter: function (v) {
                                return new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v);
                            }
                        }
                    },
                    series: [{
                        type: 'line',
                        data: montants,
                        smooth: true,
                        symbol: 'circle',
                        symbolSize: 6,
                        itemStyle: { color: '#fff' },
                        lineStyle: { color: '#fff', width: 3 },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: 'rgba(255,255,255,0.45)' },
                                { offset: 1, color: 'rgba(255,255,255,0)' }
                            ])
                        }
                    }]
                });

                window.addEventListener('resize', function () { chart.resize(); });
            })();
        </script>

        {{-- Cartes KPI à mini-graphique : objectif, natures de taxe, modes de règlement, PP/PM --}}
        <script>
            (function () {
                if (typeof echarts === 'undefined') return;

                var fmt = new Intl.NumberFormat('fr-FR');
                var palette = ['#2c7be5', '#27bcfd', '#00d27a', '#f5803e', '#e63757', '#748194'];

                // 1. Jauge — objectif de recouvrement
                var elG = document.querySelector('.echart-kpi-objectif');
                if (elG) {
                    var taux = parseFloat(elG.dataset.taux) || 0;
                    var couleur = taux >= 100 ? '#00d27a' : (taux >= 50 ? '#2c7be5' : '#f5803e');
                    var g = echarts.init(elG);
                    g.setOption({
                        series: [{
                            type: 'gauge', startAngle: 220, endAngle: -40, min: 0, max: 100,
                            radius: '95%', center: ['50%', '58%'],
                            progress: { show: true, width: 10, itemStyle: { color: couleur } },
                            axisLine: { lineStyle: { width: 10, color: [[1, 'rgba(115,129,148,0.2)']] } },
                            pointer: { show: false }, axisTick: { show: false },
                            splitLine: { show: false }, axisLabel: { show: false }, anchor: { show: false },
                            title: { show: false },
                            detail: {
                                valueAnimation: true, offsetCenter: [0, 0],
                                formatter: '{value}%', fontSize: 22, fontWeight: 'bold', color: couleur
                            },
                            data: [{ value: taux }]
                        }]
                    });
                    window.addEventListener('resize', function () { g.resize(); });
                }

                // 2. Barres horizontales — émissions par nature de taxe
                var elN = document.querySelector('.echart-kpi-natures');
                if (elN) {
                    var labelsN = @json($repartitions['natures_taxe']['labels']);
                    var dataN = @json($repartitions['natures_taxe']['montants']);
                    var n = echarts.init(elN);
                    n.setOption({
                        tooltip: {
                            trigger: 'axis', axisPointer: { type: 'shadow' },
                            formatter: function (p) { return p[0].name + '<br/><strong>' + fmt.format(p[0].value) + ' FCFA</strong>'; }
                        },
                        grid: { left: '2%', right: '8%', top: '4%', bottom: '2%', containLabel: true },
                        xAxis: { type: 'value', axisLabel: { show: false }, axisLine: { show: false }, splitLine: { show: false } },
                        yAxis: {
                            type: 'category', data: labelsN.slice().reverse(),
                            axisTick: { show: false }, axisLine: { show: false },
                            axisLabel: { color: '#748194', fontSize: 11 }
                        },
                        series: [{
                            type: 'bar', data: dataN.slice().reverse(), barWidth: '55%',
                            itemStyle: { color: '#2c7be5', borderRadius: [0, 3, 3, 0] }
                        }]
                    });
                    window.addEventListener('resize', function () { n.resize(); });
                }

                // Fabrique d'anneau (donut) réutilisable
                function donut(selecteur, labels, valeurs, monetaire) {
                    var el = document.querySelector(selecteur);
                    if (!el) return;
                    var c = echarts.init(el);
                    c.setOption({
                        color: palette,
                        tooltip: {
                            trigger: 'item',
                            formatter: function (p) {
                                var v = monetaire ? fmt.format(p.value) + ' FCFA' : fmt.format(p.value);
                                return p.name + '<br/><strong>' + v + '</strong> (' + p.percent + '%)';
                            }
                        },
                        legend: { bottom: 0, left: 'center', itemWidth: 10, itemHeight: 10, textStyle: { color: '#748194', fontSize: 11 } },
                        series: [{
                            type: 'pie', radius: ['45%', '70%'], center: ['50%', '42%'],
                            avoidLabelOverlap: true, label: { show: false }, labelLine: { show: false },
                            data: labels.map(function (l, i) { return { name: l, value: valeurs[i] }; })
                        }]
                    });
                    window.addEventListener('resize', function () { c.resize(); });
                }

                // 3. Pie Label Align (ECharts) — recouvrements par mode de règlement
                var elModes = document.querySelector('.echart-pie-modes-align');
                if (elModes) {
                    var labelsModes = @json($repartitions['modes_reglement']['labels']);
                    var montantsModes = @json($repartitions['modes_reglement']['montants']);
                    var couleursModes = ['#2c7be5', '#e63757', '#00d27a', '#27bcfd', '#f5803e', '#748194'];

                    var m = echarts.init(elModes);
                    m.setOption({
                        tooltip: {
                            trigger: 'item', padding: [7, 10],
                            backgroundColor: '#fff', borderColor: '#d8e2ef', borderWidth: 1,
                            textStyle: { color: '#5e6e82' }, transitionDuration: 0,
                            formatter: function (p) { return p.name + ' : <strong>' + fmt.format(p.value) + ' FCFA</strong> (' + p.percent + '%)'; }
                        },
                        series: [{
                            type: 'pie',
                            radius: window.innerWidth < 530 ? '45%' : '60%',
                            center: ['50%', '50%'],
                            left: '5%', right: '5%', top: 0, bottom: 0,
                            data: labelsModes.map(function (l, i) {
                                return { name: l, value: montantsModes[i], itemStyle: { color: couleursModes[i % couleursModes.length] } };
                            }),
                            label: {
                                position: 'outer', alignTo: 'labelLine', bleedMargin: 5,
                                color: '#748194', fontSize: 13, formatter: '{b}'
                            }
                        }]
                    });
                    window.addEventListener('resize', function () {
                        m.setOption({ series: [{ radius: window.innerWidth < 530 ? '45%' : '60%' }] });
                        m.resize();
                    });
                }

                // 4. Anneau — contribuables PP / PM
                donut('.echart-kpi-personnes',
                    ['Personnes physiques', 'Personnes morales'],
                    [{{ $repartitions['personnes']['physiques'] }}, {{ $repartitions['personnes']['morales'] }}],
                    false);
            })();
        </script>

        {{-- Émissions liquidées des 12 derniers mois (barres) --}}
        <script>
            (function () {
                var el = document.querySelector('.echart-emissions-12-mois');
                if (!el || typeof echarts === 'undefined') return;

                var labels = @json($emissions['labels']);
                var montants = @json($emissions['montants']);
                var fmt = new Intl.NumberFormat('fr-FR');

                var chart = echarts.init(el);
                chart.setOption({
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: { type: 'shadow' },
                        formatter: function (params) {
                            var p = params[0];
                            return p.axisValue + '<br/><strong>' + fmt.format(p.data) + ' FCFA</strong>';
                        }
                    },
                    grid: { left: '2%', right: '3%', top: '10%', bottom: '3%', containLabel: true },
                    xAxis: {
                        type: 'category',
                        data: labels,
                        axisLine: { lineStyle: { color: 'rgba(115,129,148,0.3)' } },
                        axisTick: { show: false },
                        axisLabel: { color: '#748194', fontSize: 11 }
                    },
                    yAxis: {
                        type: 'value',
                        splitLine: { lineStyle: { color: 'rgba(115,129,148,0.15)' } },
                        axisLabel: {
                            color: '#748194', fontSize: 11,
                            formatter: function (v) {
                                return new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v);
                            }
                        }
                    },
                    series: [{
                        type: 'bar',
                        data: montants,
                        barWidth: '55%',
                        itemStyle: {
                            borderRadius: [3, 3, 0, 0],
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: '#2c7be5' },
                                { offset: 1, color: '#27bcfd' }
                            ])
                        }
                    }]
                });

                window.addEventListener('resize', function () { chart.resize(); });
            })();
        </script>
    @endpush
</x-app-layout>
