<template>
    <div v-if="rates.raif.length && rates.korona.length && rates.unistream.length" class="container">
        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="big">
                    <div class="big-column">
                        <h1>{{ rates.raif[0].rate }}</h1>
                        <h2>${{ $filters.currencyFormat(3492000 / rates.raif[0].rate) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="big">
                    <div class="big-column">
                        <h1>{{ rates.korona[0].rate }}</h1>
                        <h2>${{ $filters.currencyFormat(3492000 / rates.korona[0].rate) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="big">
                    <div class="big-column">
                        <h1>{{ rates.unistream[0].rate }}</h1>
                        <h2>${{ $filters.currencyFormat(3492000 / rates.unistream[0].rate) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="best">
                    <p>Лучший курс Райффайзен:</p>

                    <div class="best-table">
                        <div class="best-table-item">
                            <div>
                                {{ $filters.datetime(min.raif.created_at) }}
                            </div>
                            <div>
                                {{ min.raif.rate }}
                            </div>
                            <div>
                                ${{ $filters.currencyFormat(3492000 / min.raif.rate) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="best">
                    <p>Лучший курс Корона:</p>

                    <div class="best-table">
                        <div class="best-table-item">
                            <div>
                                {{ $filters.datetime(min.korona.created_at) }}
                            </div>
                            <div>
                                {{ min.korona.rate }}
                            </div>
                            <div>
                                ${{ $filters.currencyFormat(3492000 / min.korona.rate) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="best">
                    <p>Лучший курс Юнистрим:</p>

                    <div class="best-table">
                        <div class="best-table-item">
                            <div>
                                {{ $filters.datetime(min.unistream.created_at) }}
                            </div>
                            <div>
                                {{ min.unistream.rate }}
                            </div>
                            <div>
                                ${{ $filters.currencyFormat(3492000 / min.unistream.rate) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="history">
                    <p>История курсов Райффайзен:</p>

                    <div class="history-table">
                        <div v-for="rateItem in rates.raif.slice(1)" class="history-table-item">
                            <div>
                                {{ $filters.datetime(rateItem.created_at) }}
                            </div>
                            <div>
                                {{ rateItem.rate }}
                            </div>
                            <div>
                                ${{ $filters.currencyFormat(3492000 / rateItem.rate) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="history">
                    <p>История курсов Корона:</p>

                    <div class="history-table">
                        <div v-for="rateItem in rates.korona.slice(1)" class="history-table-item">
                            <div>
                                {{ $filters.datetime(rateItem.created_at) }}
                            </div>
                            <div>
                                {{ rateItem.rate }}
                            </div>
                            <div>
                                ${{ $filters.currencyFormat(3492000 / rateItem.rate) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="history">
                    <p>История курсов Юнистрим:</p>

                    <div class="history-table">
                        <div v-for="rateItem in rates.unistream.slice(1)" class="history-table-item">
                            <div>
                                {{ $filters.datetime(rateItem.created_at) }}
                            </div>
                            <div>
                                {{ rateItem.rate }}
                            </div>
                            <div>
                                ${{ $filters.currencyFormat(3492000 / rateItem.rate) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            rates: {
                raif: [],
                korona: [],
                unistream: [],
            },

            min: {
                raif: '',
                korona: '',
                unistream: '',
            },
        }
    },
    created() {
        this.loadRates()
    },
    methods: {
        loadRates() {
            axios.get('/rates')
            .then(response => {
                this.rates.raif = response.data.raif_rates
                this.rates.korona = response.data.korona_rates
                this.rates.unistream = response.data.unistream_rates
                this.min.raif = response.data.raif_min
                this.min.korona = response.data.korona_min
                this.min.unistream = response.data.unistream_min
            })
        },
    },
}
</script>