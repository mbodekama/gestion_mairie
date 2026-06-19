<x-app-layout :title="__('Statistique calibrée')">

    <x-page-header titre="Pilotage — Statistique calibrée"
                   sous-titre="Construisez un graphique sur mesure : objet, période, regroupement et type de diagramme" />

    @if ($collectiviteAbsente)
        <div class="alert alert-warning border-0 d-flex align-items-center" role="alert">
            <div class="bg-warning me-3 icon-item">
                <span class="fas fa-exclamation-circle text-white fs-6"></span>
            </div>
            <p class="mb-0 flex-1">Aucune collectivité active n'est configurée : les statistiques ne peuvent pas être calculées.</p>
        </div>
    @else

        {{-- ===== Formulaire de paramétrage (présentation filtre standard) ===== --}}
        <x-filtre.card
            :action="route('pilotage.statistiques.calibree.generer')"
            :reset="route('pilotage.statistiques.calibree')"
            titre="Paramètres de la statistique">

            {{-- Objet principal (modèle 1) --}}
            <x-filtre.select name="objet" label="Objet à analyser" placeholder="— Sélectionner —">
                @foreach ($objets as $cle => $cfg)
                    <option value="{{ $cle }}" @selected(request('objet') === $cle)>{{ $cfg['libelle'] }}</option>
                @endforeach
            </x-filtre.select>

            {{-- Objet de comparaison (modèle 2, optionnel) --}}
            <x-filtre.select name="objet_compare" label="Comparer à (optionnel)" placeholder="— Aucune comparaison —">
                @foreach ($objets as $cle => $cfg)
                    <option value="{{ $cle }}" @selected(request('objet_compare') === $cle)>{{ $cfg['libelle'] }}</option>
                @endforeach
            </x-filtre.select>

            {{-- Plage de période --}}
            <x-filtre.date name="date_debut" label="Date de début" :value="$dateDebut" />
            <x-filtre.date name="date_fin" label="Date de fin" :value="$dateFin" />

            {{-- Regroupement temporel --}}
            <x-filtre.select name="granularite" label="Regroupement" placeholder="— Sélectionner —">
                @foreach ($granularites as $cle => $libelle)
                    <option value="{{ $cle }}" @selected(request('granularite') === $cle)>{{ $libelle }}</option>
                @endforeach
            </x-filtre.select>

            {{-- Type de diagramme --}}
            <x-filtre.select name="type_diagramme" label="Type de diagramme" placeholder="— Sélectionner —">
                @foreach ($diagrammes as $cle => $libelle)
                    <option value="{{ $cle }}" @selected(request('type_diagramme') === $cle)>{{ $libelle }}</option>
                @endforeach
            </x-filtre.select>

        </x-filtre.card>

        {{-- ===== Résultat ===== --}}
        @if ($resultat !== null)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <span class="fas fa-chart-area me-2 text-primary"></span>Graphique généré
                    </h5>
                </div>
                <div class="card-body">
                    @if ($resultat['vide'])
                        <div class="text-center text-muted py-5">
                            <span class="fas fa-folder-open fs-3 d-block mb-2"></span>
                            Aucune donnée ne correspond aux critères sélectionnés.
                        </div>
                    @else
                        <div id="echart-calibree" style="height: 420px;"
                             data-echart='@json($resultat)'></div>
                    @endif
                </div>
            </div>
        @endif

    @endif

    @if (!$collectiviteAbsente && $resultat !== null && !$resultat['vide'])
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var el = document.getElementById('echart-calibree');
                    if (!el || typeof echarts === 'undefined') return;

                    var cfg   = JSON.parse(el.dataset.echart);
                    var chart = echarts.init(el);
                    var fmt   = new Intl.NumberFormat('fr-FR');
                    var unite = cfg.unite ? ' ' + cfg.unite : '';

                    // Type ECharts selon le diagramme demandé.
                    var series = cfg.series.map(function (s) {
                        var serie = { name: s.nom, data: s.data };
                        if (cfg.diagramme === 'barres') {
                            serie.type = 'bar';
                            serie.barMaxWidth = 40;
                            serie.itemStyle = { borderRadius: [3, 3, 0, 0] };
                        } else {
                            serie.type = 'line';
                            serie.smooth = true;
                            serie.symbol = 'circle';
                            if (cfg.diagramme === 'aires') {
                                serie.areaStyle = { opacity: 0.25 };
                            }
                        }
                        return serie;
                    });

                    chart.setOption({
                        color: ['#2c7be5', '#e63757'],
                        tooltip: {
                            trigger: 'axis',
                            valueFormatter: function (v) { return fmt.format(v) + unite; }
                        },
                        legend: { bottom: 0, textStyle: { color: '#748194' } },
                        grid: { left: '2%', right: '4%', top: '8%', bottom: '12%', containLabel: true },
                        xAxis: {
                            type: 'category',
                            data: cfg.labels,
                            axisLabel: { color: '#748194', fontSize: 11 },
                            axisLine: { lineStyle: { color: '#e3e6ed' } }
                        },
                        yAxis: {
                            type: 'value',
                            axisLabel: {
                                color: '#748194', fontSize: 11,
                                formatter: function (v) { return fmt.format(v); }
                            },
                            splitLine: { lineStyle: { color: '#e3e6ed' } }
                        },
                        series: series
                    });

                    window.addEventListener('resize', function () { chart.resize(); });
                });
            </script>
        @endpush
    @endif

</x-app-layout>
