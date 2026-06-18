<x-app-layout :title="__('Statistiques mairie')">

    <x-page-header titre="Pilotage — Statistiques mairie"
                   :sous-titre="$collectiviteAbsente ? null : __('Synthèse de l\'activité fiscale — exercice :annee', ['annee' => $indicateurs['exercice_annee'] ?? '—'])" />

    @if ($collectiviteAbsente)
        <div class="alert alert-warning border-0 d-flex align-items-center" role="alert">
            <div class="bg-warning me-3 icon-item">
                <span class="fas fa-exclamation-circle text-white fs-6"></span>
            </div>
            <p class="mb-0 flex-1">Aucune collectivité active n'est configurée : les statistiques ne peuvent pas être calculées.</p>
        </div>
    @else
        @php
            $fcfa = fn ($v) => number_format((float) $v, 0, ',', ' ') . ' FCFA';
        @endphp

        {{-- ===== Indicateurs clés ===== --}}
        <div class="row g-3 mb-3">
            <div class="col-sm-6 col-xxl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="text-700 mb-2">Montant émis</h6>
                        <div class="d-flex align-items-center">
                            <span class="fas fa-file-invoice fs-3 text-primary me-3"></span>
                            <h4 class="mb-0">{{ $fcfa($indicateurs['montant_emis']) }}</h4>
                        </div>
                        <small class="text-muted">{{ number_format($indicateurs['nb_emissions'], 0, ',', ' ') }} émission(s)</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xxl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="text-700 mb-2">Montant recouvré</h6>
                        <div class="d-flex align-items-center">
                            <span class="fas fa-hand-holding-usd fs-3 text-success me-3"></span>
                            <h4 class="mb-0">{{ $fcfa($indicateurs['montant_recouvre']) }}</h4>
                        </div>
                        <small class="text-muted">Taux : {{ $indicateurs['taux_recouvrement'] }} %</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xxl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="text-700 mb-2">Reste à recouvrer</h6>
                        <div class="d-flex align-items-center">
                            <span class="fas fa-coins fs-3 text-warning me-3"></span>
                            <h4 class="mb-0">{{ $fcfa($indicateurs['reste_a_recouvrer']) }}</h4>
                        </div>
                        <small class="text-muted">Objectif : {{ $fcfa($repartitions['objectif']['montant']) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xxl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="text-700 mb-2">Recensement</h6>
                        <div class="d-flex align-items-center">
                            <span class="fas fa-users fs-3 text-info me-3"></span>
                            <h4 class="mb-0">{{ number_format($indicateurs['contribuables_actifs'], 0, ',', ' ') }}</h4>
                        </div>
                        <small class="text-muted">{{ number_format($indicateurs['etablissements_actifs'], 0, ',', ' ') }} établissement(s)</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Évolution émissions vs recouvrements (12 mois) ===== --}}
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h5 class="mb-0">Émissions et recouvrements — 12 derniers mois</h5>
                    </div>
                    <div class="card-body">
                        <div class="echart-stat-evolution" style="min-height:320px" data-echart-responsive="true"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            {{-- ===== Montant émis par nature de taxe ===== --}}
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header border-bottom">
                        <h5 class="mb-0">Montant émis par nature de taxe</h5>
                    </div>
                    <div class="card-body">
                        @if (empty($repartitions['natures_taxe']['labels']))
                            <p class="text-muted mb-0">Aucune émission sur l'exercice ouvert.</p>
                        @else
                            @php $totalNat = array_sum($repartitions['natures_taxe']['montants']) ?: 1; @endphp
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr><th>Nature de taxe</th><th class="text-end">Montant</th><th class="text-end">%</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($repartitions['natures_taxe']['labels'] as $i => $lib)
                                        @php $m = $repartitions['natures_taxe']['montants'][$i]; @endphp
                                        <tr>
                                            <td>{{ $lib }}</td>
                                            <td class="text-end">{{ $fcfa($m) }}</td>
                                            <td class="text-end">{{ round($m / $totalNat * 100, 1) }} %</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ===== Recouvrements par mode de règlement ===== --}}
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header border-bottom">
                        <h5 class="mb-0">Recouvrements par mode de règlement</h5>
                    </div>
                    <div class="card-body">
                        @if (empty($repartitions['modes_reglement']['labels']))
                            <p class="text-muted mb-0">Aucun recouvrement sur l'exercice ouvert.</p>
                        @else
                            @php $totalMod = array_sum($repartitions['modes_reglement']['montants']) ?: 1; @endphp
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr><th>Mode de règlement</th><th class="text-end">Montant</th><th class="text-end">%</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($repartitions['modes_reglement']['labels'] as $i => $lib)
                                        @php $m = $repartitions['modes_reglement']['montants'][$i]; @endphp
                                        <tr>
                                            <td>{{ $lib }}</td>
                                            <td class="text-end">{{ $fcfa($m) }}</td>
                                            <td class="text-end">{{ round($m / $totalMod * 100, 1) }} %</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            {{-- ===== Structure des contribuables ===== --}}
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header border-bottom">
                        <h5 class="mb-0">Structure des contribuables actifs</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $pp = $repartitions['personnes']['physiques'];
                            $pm = $repartitions['personnes']['morales'];
                            $totalPers = ($pp + $pm) ?: 1;
                        @endphp
                        <div class="echart-stat-personnes" style="min-height:220px" data-echart-responsive="true"></div>
                        <div class="d-flex justify-content-around mt-3">
                            <div class="text-center">
                                <h6 class="text-700 mb-0">Personnes physiques</h6>
                                <h4 class="mb-0">{{ number_format($pp, 0, ',', ' ') }}</h4>
                                <small class="text-muted">{{ round($pp / $totalPers * 100, 1) }} %</small>
                            </div>
                            <div class="text-center">
                                <h6 class="text-700 mb-0">Personnes morales</h6>
                                <h4 class="mb-0">{{ number_format($pm, 0, ',', ' ') }}</h4>
                                <small class="text-muted">{{ round($pm / $totalPers * 100, 1) }} %</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Top contribuables ===== --}}
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header border-bottom">
                        <h5 class="mb-0">Top contribuables (montant recouvré)</h5>
                    </div>
                    <div class="card-body">
                        @if (empty($topContribuables))
                            <p class="text-muted mb-0">Aucun recouvrement enregistré.</p>
                        @else
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr><th>Contribuable</th><th>N°</th><th class="text-end">Recouvré</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($topContribuables as $c)
                                        <tr>
                                            <td>
                                                <span class="badge bg-soft-{{ $c['type_personne'] === 'PM' ? 'info' : 'primary' }} text-{{ $c['type_personne'] === 'PM' ? 'info' : 'primary' }} me-1">{{ $c['type_personne'] }}</span>
                                                {{ $c['nom_affiche'] }}
                                            </td>
                                            <td class="text-muted">{{ $c['numero'] }}</td>
                                            <td class="text-end">{{ $fcfa($c['total']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                (function () {
                    if (typeof echarts === 'undefined') return;
                    var fmt = new Intl.NumberFormat('fr-FR');
                    var compact = new Intl.NumberFormat('fr-FR', { notation: 'compact' });

                    // 1. Évolution émissions vs recouvrements (barres groupées)
                    var elEvo = document.querySelector('.echart-stat-evolution');
                    if (elEvo) {
                        var chart = echarts.init(elEvo);
                        chart.setOption({
                            tooltip: {
                                trigger: 'axis',
                                formatter: function (params) {
                                    var s = params[0].axisValue;
                                    params.forEach(function (p) {
                                        s += '<br/>' + p.marker + p.seriesName + ' : <strong>' + fmt.format(p.data) + ' FCFA</strong>';
                                    });
                                    return s;
                                }
                            },
                            legend: { data: ['Émissions', 'Recouvrements'], top: 0 },
                            grid: { left: '2%', right: '3%', top: '15%', bottom: '3%', containLabel: true },
                            xAxis: { type: 'category', data: @json($emissions['labels']), axisTick: { show: false } },
                            yAxis: { type: 'value', axisLabel: { formatter: function (v) { return compact.format(v); } } },
                            series: [
                                { name: 'Émissions', type: 'bar', data: @json($emissions['montants']), itemStyle: { color: '#2c7be5', borderRadius: [3, 3, 0, 0] } },
                                { name: 'Recouvrements', type: 'bar', data: @json($recouvrements['montants']), itemStyle: { color: '#00d27a', borderRadius: [3, 3, 0, 0] } }
                            ]
                        });
                        window.addEventListener('resize', function () { chart.resize(); });
                    }

                    // 2. Structure contribuables PP / PM (anneau)
                    var elPers = document.querySelector('.echart-stat-personnes');
                    if (elPers) {
                        var c2 = echarts.init(elPers);
                        c2.setOption({
                            tooltip: { trigger: 'item', formatter: '{b} : {c} ({d}%)' },
                            series: [{
                                type: 'pie', radius: ['55%', '80%'], avoidLabelOverlap: false,
                                label: { show: false }, labelLine: { show: false },
                                data: [
                                    { value: {{ $pp }}, name: 'Personnes physiques', itemStyle: { color: '#2c7be5' } },
                                    { value: {{ $pm }}, name: 'Personnes morales', itemStyle: { color: '#27bcfd' } }
                                ]
                            }]
                        });
                        window.addEventListener('resize', function () { c2.resize(); });
                    }
                })();
            </script>
        @endpush
    @endif

</x-app-layout>
