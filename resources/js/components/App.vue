<template>
    <div v-if="rates.length" class="container">
        <div class="row">
            <div class="col-12">
                <div class="big">
                    <div class="big-column">
                        <h1>{{ rates[0].rate }}</h1>
                        <h2>${{ $filters.currencyFormat(3492000 / rates[0].rate) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="best">
                    <p>Лучший курс:</p>

                    <div class="best-table">
                        <div class="best-table-item">
                            <div>
                                {{ $filters.datetime(minRate.created_at) }}
                            </div>
                            <div>
                                {{ minRate.rate }}
                            </div>
                            <div>
                                ${{ $filters.currencyFormat(3492000 / minRate.rate) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="history">
                    <p>История курсов:</p>

                    <div class="history-table">
                        <div v-for="rateItem in rates.slice(1)" class="history-table-item">
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
            rates: [],

            minRate: '',
        }
    },
    created() {
        this.loadRates()
    },
    methods: {
        loadRates() {
            axios.get('/rates')
            .then(response => {
                this.rates = response.data.rates
                this.minRate = response.data.min
            })
        },
    },
}
</script>