<template>
    <div class="row">
        <div class="col-2" >
            <sideBar 
                @loadRoute="loadRoute($event)"
                :cliente='cliente'>
            </sideBar>
        </div>
        <div class="col-10">
            <div class="container-fluid my-4" style="padding-left: 5% !important;  padding-right: 5% !important; ">
                <div class="row">
                    <!-- <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fas fa-align-left"></i>
                    </button> -->
                    <div class="col-12 text-center">
                        <h3 class="center">Asignación</h3>
                    </div>
                </div>

                <!-- Opciones Obligatorias -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-3">
                                <label for="">Base de Operacion</label>
                                <select :style="[base_de_operaciones=='' ? {'border':'1px solid red'} : {}]" class="form-control" id="selectBase" @change="changeInitialOrigin($event)" :value="base_de_operaciones">
                                    <option selected disabled value="">--</option>
                                    <option v-for="base in data.bases" :key="base.id" :value="base.direccion">{{base.nombre}}</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label for="">Cliente</label>
                                <select :style="[cliente=='' ? {'border':'1px solid red'} : {}]" class="form-control" id="selectCliente" @change="changeInitialDestiny">
                                    <option selected disabled value="">--</option>
                                    <option v-for="cliente in data.clientes" :key="cliente.id" :value="cliente.domCarga">{{cliente.nombre}}</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label for="">Tipos de unidades</label>
                                <select :style="[tipoDeUnidad=='' ? {'border':'1px solid red'} : {}]" class="form-control" id="selectTipoUnidad" @change="changeTipoDeUnidades($event)" v-model="tipoDeUnidad">
                                    <option selected disabled value="">--</option>
                                    <option v-for="(tipo , index) in data.tipos" :key="index" :value="tipo.tipo">{{tipo.tipo}}</option>
                                </select>
                            </div>
                            
                            <div class="col-3">
                                <label for="">Fecha de Salida</label><br>
                                <date-picker :style="[validFechaSalida ? {'border':'1px solid red'} : {}]" disabled v-model="fechaSalida" format="DD-MM-YYYY  HH:mm"  type="datetime" lang="lang"></date-picker>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-3">
                                <label for="">Modelo</label>
                                <select :style="[tonelaje=='' ? {'border':'1px solid red'} : {}]" class="form-control" id="selectTonelaje" @change="changeTonelaje($event)" v-model="tonelaje">
                                    <option selected disabled value="">--</option>
                                    <option v-for="toneladas in data.toneladas" :key="toneladas.id" :value="toneladas.toneladas">{{toneladas.toneladas}}</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label class="" for="ejes">Ejes</label>
                                <select class="form-control" id="selectEjes" @change="changeEjes($event)" v-model="ejes">
                                    <option selected disabled value=""></option>
                                    <option value="2AxlesTruck">2</option>
                                    <option value="3AxlesTruck">3</option>
                                    <option value="4AxlesTruck">4</option>
                                    <option value="5AxlesTruck">5</option>
                                    <option value="6AxlesTruck">6</option>
                                    <option value="7AxlesTruck">7</option>
                                    <option value="8AxlesTruck">8</option>
                                    <option value="9AxlesTruck">9</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label class="" for="diesel">Diesel</label>
                                <input class="form-control" type="text" name="diesel" v-model="diesel" value="">
                            </div>
                            <div class="col-2">
                                <label class="">Validar Fecha</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" checked class="custom-control-input" id="customSwitch1" @change="toggleValidDate">
                                    <label class="custom-control-label" for="customSwitch1"></label>
                                </div>
                            </div>
                            <div class="col-1">
                                <label class="">Viaje R</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" checked class="custom-control-input" id="customSwitch2" @change="toggleRoundTrip">
                                    <label class="custom-control-label" for="customSwitch2"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-for="(route, index) in routes" :key="index">
                    <tramo 
                        :index="index" 
                        :max="routes.length" 
                        :isRoundTrip="isRoundTrip" 
                        :tollGurukey="data.tollGurukey[0]"
                        :route="route" 
                        :previousRoute="index==0 ? null : (routes[index-1] ? routes[index-1] : null)"
                        :ejes="ejes"
                        ref="ref_routes"
                        @tramoDeleted="removeTramo($event)"
                        @deleteBaseAndClient="deleteBaseAndClient($event)"
                        @routeUpdated="routeChange($event,index)">
                        
                    </tramo>
                    <hr>
                </div>
                
                <!-- Operadores Unidades -->
                <div class="row mt-4 mr-2 ml-1 justify-content-end">
                    
                    <!-- Btn Add Tramo -->
                    <div class="col-4">
                        <label for="">Operadores</label>
                        <select class="form-control" id="selectOperador" :v-model="operador" @change="changeOperador($event)">
                            <option selected disabled value="">--</option>
                            <option v-for="operador in data.operadores" :key="operador.id" :value="operador.nombre">{{operador.nombre}}</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label for="">Unidades</label>
                        <select class="form-control" id="selectUnidad" :v-model="unidad" @change="changeUnidad($event)">
                            <option selected disabled value="">--</option>
                            <option v-for="unidad in data.unidades" :key="unidad.id" :value="unidad.camion">{{unidad.camion}} Ejes: {{unidad.ejes}}</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <button @click="addRoute($event)" class="col-12 btn btn-primary addTramo">Agregar Tramo</button>
                    </div>
                </div>

                <!-- Button Planear -->
                <div class="row mt-4 justify-content-center">
                    <button :disabled="emptyInputs || validFechaSalida" @click="createMap" class="col-6 btn btn-primary" data-toggle="modal" data-target="#modal-map">
                        Planear Ruta
                    </button>
                </div>
                
            </div>
        </div>

        <!-- Modal -->

        <div class="modal fade" id="modal-map" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Planear Ruta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" v-for="(route, index) in routes" :key="index">
                            <div class="row">
                                <label class="col-6" v-if="index == 0" for="">Fecha de Carga</label>
                                <label class="col-6" v-if="index != 0 && index != (routes.length-1)" for="">Fecha de Entrega {{index}}</label>
                                <label class="col-6" v-if="index == (routes.length-1)" for="">Fecha de Disponibilidad</label>
                                <date-picker class="col-6" disabled v-model="route.fecha" format="DD-MM-YYYY  HH:mm"  type="datetime" lang="lang"></date-picker>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4">
                            <label for="">Tiempo</label>
                            <input disabled :value="total_tiempo" type="text" class="form-control" >
                        </div>
                        <div class="col-4">
                            <label for="">Kms</label>
                            <input disabled :value="totalDistanceDisplay" type="text" class="form-control">
                        </div>
                        <div class="col-2">
                            <label for="">Casetas</label>
                            <input disabled :value="total_casetas" type="text" class="form-control" id="">
                        </div>
                        <div class="col-2">
                            <label style="visibility:hidden;" for="">Casetas</label>
                            <button class="form-control" data-toggle="modal" data-target="#modal-casetas">Ver Casetas</button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4">
                            <label for="">Operador</label>
                            <input disabled :value="operador" type="text" class="form-control">
                        </div>
                        <div class="col-4">
                            <label for="">Unidad</label>
                            <input disabled :value="unidad" type="text" class="form-control">
                        </div>
                        <div class="col-4">
                            <label for="">Ciudad</label>
                            <select class="form-control" id="selectCliente" @change="changeCiudad">
                                <option selected disabled value="">--</option>
                                <option v-for="ciudad in data.ciudades" :key="ciudad.id" :value="ciudad.nombre">{{ciudad.nombre}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div style="height:500px;" id="main-map"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <label v-if="saveTrip">Nombre Viaje</label>
                    <input v-if="saveTrip" v-model="tripName" type="text" class="form-control">
                    <div class="custom-control custom-switch">
                        <input :checked="saveTrip" type="checkbox" class="custom-control-input" id="customSwitch3" @change="toggleSaveTrip">
                        <label class="custom-control-label" for="customSwitch3">Salvar Ruta</label>
                    </div>
                    <button type="button" @click="submitData" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
                </div>
            </div>
        </div>

        <!-- Modal Casetas -->
        <div class="modal fade" id="modal-casetas" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        Casetas
                    </div>
                    <div class="modal-body">
                        <div v-for="(route, index) in routes" :key="index">
                            <div class="row">
                                <div class="col-12">
                                    Casetas del Tramo No: {{index + 1}}
                                    <div class="row" v-for="(caseta, ind) in route.desgloze_casetas" :key="ind">
                                        <div style="font-size:11px;" class="col-6">Nombre: {{caseta.name}}</div>
                                        <div style="font-size:11px;" class="col-6">Costo: {{caseta.cashCost}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>

    import DatePicker from 'vue2-datepicker'
    import tramo from './tramo.vue';
    import sideBar from './sideBar.vue';
    import Vue from 'vue';

    export default {

        data() {
            return {
                data:{
                    clientes:{},
                    bases:{},
                    modelos:{},
                    tipos:{},
                    toneladas:{},
                    operadores:{},
                    unidades:{},
                    diesel:{},
                    tollGurukey:{},
                },
                horaSalida:'',
                emptyInputs:true,
                routes:[],
                base_de_operaciones:'',
                cliente:'',
                operador:'',
                unidad:'',
                tonelaje:'',
                tipoDeUnidad:'',
                isRoundTrip:true,
                ejes:'2AxlesTruck',
                ciudad:'',
                diesel:'',
                fecha_disponibilidad:'',
                fecha_carga:'',
                checkValidDate:true,                
                numero_de_tramos:1,
                saveTrip:false,
                tripName:'',
                nuevo_map:'',
            }
        },
        created(){
            this.addRoute(null);
            this.addRoute(null);
            this.addRoute(null);
        },
        mounted(){
            this.getAllData();
        },
        computed: {
            validFechaSalida: function(){
                if(this.checkValidDate){
                    if(Date.parse(this.fechaSalida) < Date.parse(new Date())){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
                
            },
            totalTimeDisplay: function(){
                var distance = this.total_distancia || 0
                var distance_in_kms = distance / 1000;
                var time = Math.floor(distance_in_kms / 60) + "h " + Math.floor((distance_in_kms % 60)) + "m "      
                return time
            },

            totalDistanceDisplay: function(){
                return Math.round(this.total_distancia / 1000) + " km" || "0 km"
            },

            fechaDisponibilidad: function(){
                return this.routes[(this.routes.length) - 1].fecha
            },

            fechaCarga:function(){
                return this.convertDateTimeToString(new Date(Date.parse(this.fecha_carga)));
            },
            fechaSalida:function(){
                var distance = this.routes[0].distancia || 0
                var distance_in_kms = distance / 1000;
                var hours = Math.floor(distance_in_kms / 60)
                var minutes = Math.floor(distance_in_kms % 60)
                return new Date(Date.parse(this.fecha_carga) - (hours * 3.6e+6 ) - (minutes * 60000 )) 
            },
            total_distancia:function(){
                var total = 0;
                var routes = JSON.parse(JSON.stringify(this.routes))
                routes.forEach(function (item){
                    total += item.distancia
                })
                return total;
            },
            total_tiempo:function(){
                var miliseconds = Math.abs(new Date(this.fechaDisponibilidad) - new Date(this.fechaSalida))
                return this.convertMiliseconds(miliseconds);
            },
            total_casetas:function(){
                var total = 0;
                var routes = JSON.parse(JSON.stringify(this.routes))
                routes.forEach(function (item){
                    total += item.casetas
                })
                return total;
            }

        },
        watch: {
            'data.diesel':{
                handler:function(val,oldval){
                    if (val[0]) {
                        this.diesel = val[0].precio;
                    }
                },
            },
            ejes:{
                handler:function(val,oldval){
                    this.updateAllAxlesRoutes(val);
                },
            },
            fecha_disponibilidad:{
                handler:function(val,oldval){
                    this.handleDateChangeForOperators();
                    this.handleDataChangeForUnits();
                }
            },
            fecha_carga:{
                handler:function(val,oldval){
                    this.handleDateChangeForOperators();
                    this.handleDataChangeForUnits();
                }
            },
            tonelaje:{
                handler:function(val,oldval){
                    this.handleDataChangeForUnits();
                }
            },
            tipoDeUnidad:{
                handler:function(val,oldval){
                    this.handleDataChangeForUnits();
                }
            },
            routes: {
                deep:true,
                handler:function (routes) {

                    this.emptyInputs = checkInputs();

                    function checkInputs(){
                        for(var route in routes){
                            for (var value in routes[route]){
                                if (value != "observaciones") {
                                    if (routes[route][value] === "") {
                                        return true;
                                    }   
                                }
                            }
                        }
                        return false;
                    }

                }
            }
        },
        methods: {
            deleteBaseAndClient(event){
                this.base_de_operaciones = '';
                this.cliente = '';
            },
            changeCiudad(event){
                this.ciudad = event.target.value;
            },
            changeEjes(event){
                this.ejes = event.target.value;
            },
            handleDateChangeForOperators(){
                var dataArray = {
                    "fecha_inicial":this.fechaCarga,
                    "fecha_final":this.fechaDisponibilidad,
                }

                fetch('http://www.misistema.mx/beluga/Finanzas/endpoints/get/conductores.php',{
                    method:'POST',
                    body:JSON.stringify(dataArray), 
                })
                .then(res => res.json())
                .then(data => {
                    this.data.operadores = data
                });
            },
            handleDataChangeForUnits(){

                var dataArray = {
                    "fecha_inicial":this.fechaCarga,
                    "fecha_final":this.fechaDisponibilidad,
                    "toneladas":this.tonelaje,
                    "tipo":this.tipoDeUnidad,
                }

                fetch('http://www.misistema.mx/beluga/Finanzas/endpoints/get/modelosActivos.php',{
                    method:'POST',
                    body:JSON.stringify(dataArray), 
                })
                .then(res => res.json())
                .then(data => {
                    this.data.unidades = data
                });
            },
            changeNextOrigin(address,index){
                if(this.routes[index+1]){
                    this.routes[index+1].origen = address;
                }
            },
            finalRouteChange(fecha){
                this.fecha_disponibilidad = fecha;
            },
            changeUnidad(event){
                this.unidad = $('#selectUnidad').children('option:selected').text();
            },
            changeOperador(event){
                this.operador = $('#selectOperador').children('option:selected').text();
            },
            routeChange(route,index){

                if(this.routes[index]){
                    this.routes[index].origen = route.origen
                    this.routes[index].destino = route.destino
                    this.routes[index].fecha = route.fecha
                    this.routes[index].tiempo = route.tiempo
                    this.routes[index].distancia = route.distancia
                    this.routes[index].casetas = route.casetas
                    this.routes[index].polyline = route.polyline
                    this.routes[index].load_time = route.load_time
                    this.routes[index].observaciones = route.observaciones
                    this.routes[index].casetas = route.casetas
                }

                if(index==0){
                    this.fecha_carga = route.fecha
                }

                if(index==this.routes.length -1 ){
                    this.fecha_disponibilidad = route.fecha;    
                }
            },
            changeInitialOrigin(event){
                this.base_de_operaciones = event.target.value;
                
                this.routes[0].origen = event.target.value;
                if(this.isRoundTrip){
                    this.routes[this.routes.length-1].destino = event.target.value;
                }

            },
            changeTipoDeUnidades(event){
                this.tipoDeUnidad = event.target.value
            },
            changeTonelaje(event){
                this.tonelaje = event.target.value
            },
            changeInitialDestiny(){
                var cliente = $('#selectCliente').children('option:selected').text();
                this.cliente = cliente;
                var address = $('#selectCliente').children('option:selected').val();
                this.routes[0].destino = address;
            },
            submitData(){
                var dataArray = {
                    rutas:this.routes,
                    ejes:this.ejes,
                    ciudad:this.ciudad,
                    base_de_operaciones:this.base_de_operaciones,
                    cliente:this.cliente,
                    operador:this.operador,
                    unidad:this.unidad,
                    tonelaje:this.tonelaje,
                    tipoDeUnidad:this.tipoDeUnidad,
                    isRoundTrip:this.isRoundTrip,
                    diesel:this.diesel,
                    fecha_salida:this.fechaSalida,
                    fecha_disponibilidad:this.fecha_disponibilidad,
                    fecha_carga:this.fecha_carga,
                    total_distancia:this.total_distancia,
                    total_casetas:this.total_casetas,
                    total_gasolina:this.total_gasolina,
                    total_tiempo:this.total_tiempo,
                    numero_de_tramos:this.numero_de_tramos,
                    checkValidDate:this.checkValidDate,
                    saveTrip:this.saveTrip,
                    tripName:this.tripName,
                }

                fetch('http://www.misistema.mx/beluga/Finanzas/endpoints/post/planear.php',{
                    method:'POST',
                    body:JSON.stringify(dataArray),
                })
                .then(res => {
                    if(res.status == '200'){
                        let timerInterval;
                        Swal.fire({
                            type:'success',
                            title: 'Ruta asignada y salvada!',
                            timer: 3000,
                            showConfirmButton: false,
                            timerProgressBar: true,
                            onBeforeOpen: () => {
                                timerInterval = setInterval(() => {
                                Swal.getContent().querySelector('b')
                                    .textContent = Swal.getTimerLeft()
                                }, 100)
                            },
                            onClose: () => {
                                clearInterval(timerInterval)
                            }
                        }).then((result)=>{
                            // var url = '../Plantilla3.php'
                            // var win = window.open(url, '_self');
                            // win.focus();
                        });
                    }else{
                        Swal.fire({
                            type: 'error',
                            title: 'Error',
                            text: 'Algo ha ido mal, revise que los campos esten completos'
                        })
                    }
                });

                
            },
            toggleSaveTrip(){
                this.saveTrip = !this.saveTrip;
            },
            toggleValidDate(){
                this.checkValidDate = !this.checkValidDate;
            },
            toggleRoundTrip(){
                this.isRoundTrip = !this.isRoundTrip;
                if(this.isRoundTrip){
                    this.addRoute();
                }else{
                    this.routes.pop();
                }
            },
            loadRoute(event){
                var loaded_routes = (JSON.parse(JSON.stringify(event)));

                console.log(loaded_routes)
                var loaded_base = loaded_routes.travel[0].base_operaciones

                for(var base in this.data.bases){
                    if(loaded_base == (this.data.bases[base].nombre)){
                        console.log("entra")
                        console.log(this.data.bases[base].direccion)
                        this.base_de_operaciones = this.data.bases[base].direccion
                    }
                }

                var tracts = loaded_routes.tracts

                this.routes[0].origen = tracts[0].origen
                this.routes[0].destino = tracts[0].destino
                this.routes[0].polyline = tracts[0].polyline

                for(var y = tracts.length; y>1 ; y--){
                    this.routes.pop();
                }

                for(var x = 1; x<tracts.length ; x++){

                    this.routes.push({
                        origen:tracts[x].origen,
                        destino:tracts[x].destino,
                        fecha:'',
                        tiempo:0,
                        distancia:0,
                        casetas:0,
                        polyline:tracts[x].polyline,
                        load_time:1,
                        observaciones:'',
                        desgloze_casetas:''
                    });
                }

            },
            addRoute(event){
                this.routes.splice(this.routes.length -1 ,0,{
                    origen:'',
                    destino:'',
                    fecha:'',
                    tiempo:0,
                    distancia:0,
                    casetas:0,
                    polyline:'[]',
                    load_time:1,
                    observaciones:'',
                    desgloze_casetas:'[]'
                });
            },
            removeTramo(index){
                this.routes.splice(index,1);
            },
            createMap(){
                
                var map = new google.maps.Map(document.getElementById('main-map'), {
                    center: {lat: 23.85, lng: -102.65},
                    zoom: 5
                });

                var directionsService = new google.maps.DirectionsService();

                function renderDirections(result) { 
                    var polylineOptionsActual = {
                        strokeColor: '#'+Math.floor(Math.random()*16777215).toString(16),
                        strokeOpacity: 1.0,
                        strokeWeight: 4
                    };
                    var directionsRenderer = new google.maps.DirectionsRenderer({
                        polylineOptions: polylineOptionsActual,
                        suppressMarkers: true,
                        map: map,
                    });
                    directionsRenderer.setDirections(result); 
                }     

                function requestDirections(start, end, waypoints) { 
                    
                    directionsService.route({ 
                        origin: start, 
                        destination: end, 
                        waypoints: JSON.parse(waypoints),
                        optimizeWaypoints: true,
                        travelMode: "DRIVING"
                    }, function(result) { 
                        renderDirections(result); 
                    }); 
                } 


                for(var ruta in this.routes){
                    requestDirections(this.routes[ruta].origen,this.routes[ruta].destino,this.routes[ruta].polyline)
                }
            },
            getAllData(){
                this.data.clientes = this.getApiData('http://www.misistema.mx/beluga/Finanzas/endpoints/get/clientes.php');
                this.data.bases = this.getApiData('http://www.misistema.mx/beluga/Finanzas/endpoints/get/baseDeOperaciones.php');
                this.data.tipos = this.getApiData('http://www.misistema.mx/beluga/Finanzas/endpoints/get/tipo.php');
                this.data.toneladas = this.getApiData('http://www.misistema.mx/beluga/Finanzas/endpoints/get/toneladas.php');
                this.data.diesel = this.getApiData('http://www.misistema.mx/beluga/Finanzas/endpoints/get/disel.php');
                this.data.tollGurukey = this.getApiData('http://www.misistema.mx/beluga/Finanzas/endpoints/get/key.php');
                this.data.ciudades = this.getApiData('http://www.misistema.mx/beluga/Finanzas/endpoints/get/ciudades.php');
            },
            getApiData(url){
                let array = [];

                var request = new XMLHttpRequest()
                request.open('GET', url)
                request.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
                request.onload = function() {
                    // Begin accessing JSON data here
                    var data = JSON.parse(this.response)
                    if (request.status >= 200 && request.status < 400) {
                        data.forEach(datos => {
                            array.push(datos);
                        })
                    } else {
                        console.log('error')
                    }
                }
                request.send()
                return array;
            },
            convertDateTimeToString(dateTimeSelected){
                var year = dateTimeSelected.getFullYear();
                var month = (dateTimeSelected.getMonth()+1) > 9 ? (dateTimeSelected.getMonth()+1) : '0' + (dateTimeSelected.getMonth()+1);
                var day = dateTimeSelected.getDate() > 9 ? dateTimeSelected.getDate() : '0' + dateTimeSelected.getDate();
                var date = year + '-' + month + '-' + day;
                var hours = dateTimeSelected.getHours() > 9 ? dateTimeSelected.getHours() : '0' + dateTimeSelected.getHours();
                var minutes = dateTimeSelected.getMinutes() > 9 ? dateTimeSelected.getMinutes() : '0' + dateTimeSelected.getMinutes();
                var seconds = dateTimeSelected.getSeconds() > 9 ? dateTimeSelected.getSeconds() : '0' + dateTimeSelected.getSeconds();
                var time = hours + ":" + minutes + ":" + seconds;
                var dateTimeString = date+' '+time;
                return dateTimeString;
            },
            convertMiliseconds(miliseconds, format) {
                var days, hours, minutes, seconds, total_hours, total_minutes, total_seconds;
                
                total_seconds = parseInt(Math.floor(miliseconds / 1000));
                total_minutes = parseInt(Math.floor(total_seconds / 60));
                total_hours = parseInt(Math.floor(total_minutes / 60));
                days = parseInt(Math.floor(total_hours / 24));

                seconds = parseInt(total_seconds % 60);
                minutes = parseInt(total_minutes % 60);
                hours = parseInt(total_hours % 24);
                
                switch(format) {
                    case 's':
                        return total_seconds;
                    case 'm':
                        return total_minutes;
                    case 'h':
                        return total_hours;
                    case 'd':
                        return days;
                    default:
                        return days + "dias " + hours + "hrs " + minutes + "m " + seconds + "s";
                }
            },
        },
        components: {
            DatePicker,
            tramo,
            sideBar,
        }

    }
</script>