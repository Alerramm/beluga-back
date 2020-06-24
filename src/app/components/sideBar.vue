<template>
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-12 text-left">
                <h4>Rutas Guardadas</h4>
            </div>
        </div>
        <div class="row p-3">
            <div class="col-12 p-0 mb-2">
                <h6>Buscar</h6>
                <input v-model="filter" class="form-control"></input>
            </div>
            <h5 class="card-title">Cliente {{cliente}}</h5>
            <div v-for="(trip, index) in trips" :key="index" class="my-2 row">
                <div v-if="filterString(trip.name)" class="card col-12" style="">
                    <div class="card-body text-left">
                        <h5>{{trip.name}}</h5>
                        <p class="card-text">
                            <div style="font-size:10px;" v-for="(tract, index2) in trip.tracts" :key="index2" class="my-2">
                                <strong>Numero de Tramo {{index2}}</strong>
                                <strong>Origen:</strong>{{tract.origen}}</br>
                                <strong>Destino:</strong>{{tract.destino}}
                            </div>
                        </p>
                        <a @click="loadRoute(index)" style="font-size:10px;" href="#" class="btn btn-primary">Cargar Ruta</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    

</template>

<script>

export default {
    props:{
        cliente:String,
    },
    data (){
        return {
            trips:[],
            filter:''
        }
    },
    watch: {
        cliente:{
            handler:function(val,oldval){

                this.trips = [];
                var data = {
                    "cliente":this.cliente,
                }

                fetch('http://www.misistema.mx/beluga/Finanzas/endpoints/get/trip.php',{
                    method:'POST',
                    body:JSON.stringify(data), 
                })
                .then(res => res.json())
                .then(data => {
                    for (var trip in data){
                        this.trips.push(data[trip]);
                    }
                });
                
            },
        }
    },
    methods: {
        loadRoute(index){
            this.$emit('loadRoute',this.trips[index]);
        },
        filterString(string){
            return string.includes(this.filter);
        }
    }
}

</script>