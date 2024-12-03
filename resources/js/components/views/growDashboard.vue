<template>
    <div class="row">
                        <div v-if="plantStats.length !== 0" class="col-md-12">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-info">
                                                        <i class="hotbox-icon hotbox-icon-leaf-edit"></i>
                                                    </div>
                                                    <h3 class="info-title">{{plantStats[3].count}}</h3>
                                                    <h6 class="stats-title">
                                                        <a href="/admin/plants/plantbatch">Plant Batches</a>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-primary">
                                                        <i class="hotbox-icon hotbox-icon-plant-leaf"></i>
                                                    </div>
                                                    <h3 class="info-title">{{plantStats[0].count}}</h3>
                                                    <h6 class="stats-title">
                                                        <a href="/admin/plants/plant">Growing Plants</a>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-success">
                                                        <i class="hotbox-icon hotbox-icon-plant-vase"></i>
                                                    </div>
                                                    <h3 class="info-title">{{plantStats[1].count}}</h3>
                                                    <h6 class="stats-title">
                                                        <router-link :to="{name:'plant',params:{filters:{growth_phase:[plantStats[1].name]}}}" tag="a"><span style="white-space:nowrap;">Vegatative Plants</span></router-link>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-info">
                                                        <i class="hotbox-icon hotbox-icon-mjleaf"></i>
                                                    </div>
                                                    <h3 class="info-title">{{plantStats[2].count}}</h3>
                                                    <h6 class="stats-title">
                                                        <router-link :to="{name:'plant',params:{filters:{growth_phase:[plantStats[2].name]}}}" tag="a"><span style="white-space:nowrap;">Flowering Plants</span></router-link>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="col-md-2">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-success">
                                                        <i class="hotbox-icon hotbox-icon-scissors"></i>
                                                    </div>
                                                    <h3 class="info-title">{{plantStats[4].count}}</h3>
                                                    <h6 class="stats-title">
                                                        <a href="/admin/plants/harvest">Harvests</a>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="statistics">
                                                <div class="info">
                                                    <div class="icon icon-success">
                                                        <i class="hotbox-icon hotbox-icon-box"></i>
                                                    </div>
                                                    <h3 class="info-title">{{plantStats[5].count}}</h3>
                                                    <h6 class="stats-title">
                                                        <a href="/admin/outgoing/package">Packages</a>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
    </div>
</template>

<script>

    import ClickConfirm from 'click-confirm';
    import Chartist from 'chartist';

    export default {
        data(){
            return {
                isLoading:false,
                plantStats: []
            };
        },
        components : { 
            ClickConfirm
        },
        mounted() {
                if (this.plantStats.length === 0) {
                    axios.get('/api/v1/admin/schemas/grow/plant')
                        .then(response => {  
                            if (response.data) { 
                                this.plantStats = response.data.plantSchema.agg;
                             }
                        }).catch(error => {
                        console.log(error);
                    });
                }

        },
        methods: {

        },
        computed: {
            
        }
    };
</script>
<style>
    .strikethrough{
      text-decoration: line-through;
    }
</style>
