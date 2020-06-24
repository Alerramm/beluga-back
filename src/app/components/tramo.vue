<template>
  <div class="row mt-4">
    <div class="col-12">
      <div class="row justify-content-between pl-3 pr-4">
        <h6>{{tramoLabel}}</h6>
        <a @click="deleteTramo" class="close_tramo" v-if="tipoTramo=='default'">
          <i class="material-icons">close</i>
        </a>
      </div>
    </div>
    <div class="col-12">
      <div class="row">
        <div class="col-6">
          <label for="origen">Origen</label>
          <input
            :disabled="tipoTramo!='inicial'"
            ref="autocomplete_origin"
            :id="'origen-'+index"
            :value="route.origen"
            class="form-control"
            placeholder="Agregue Origen"
          />
        </div>
        <div class="col-6">
          <label for="destino">Destino</label>
          <input
            :disabled="tipoTramo=='final'"
            ref="autocomplete_destino"
            :id="'destino-'+index"
            :value="route.destino"
            class="form-control"
            placeholder="Agregue Destino"
          />
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="row">
        <div class="col-6">
          <div class="row">
            <div class="col-4">
              <label for>Tiempo</label>
              <input disabled :value="timeToDisplay" type="text" class="form-control" />
            </div>
            <div class="col-5">
              <label for>{{fechaLabel}}</label>
              <date-picker
                :not-before="calculateMinDate"
                :confirm="true"
                @change="changeDate($event)"
                format="DD-MM-YYYY   HH:mm"
                v-model="route.fecha"
                type="datetime"
                lang="en"
              ></date-picker>
            </div>
            <div class="col-3" v-if="this.tipoTramo=='final'"></div>
            <div class="col-3" v-if="this.tipoTramo!='final'">
              <label for>Hora de Recibo</label>
              <select @change="changeHours" name id="select_hours" class="form-control">
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
                <option>6</option>
                <option>7</option>
                <option>8</option>
              </select>
            </div>
          </div>
        </div>
        <div class="col-2">
          <label for>Kms</label>
          <input
            disabled
            :value="distanceToDisplay"
            type="text"
            class="form-control"
            :id="'distance'+index"
          />
        </div>
        <div class="col-2">
          <label for>Casetas</label>
          <input disabled :value="route.casetas" type="text" class="form-control" id />
        </div>
        <div class="col-2 mt-4 p-2">
          <button
            @click="createMap"
            type="button"
            class="col-12 btn btn-primary"
            data-toggle="modal"
            :data-target="'#modal-map-'+index"
          >Tramo</button>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <label for>Observaciones</label>
          <input v-model="route.observaciones" class="form-control" type="text" />
        </div>
      </div>
    </div>
    <div class="modal fade" :id="'modal-map-'+index" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">{{tramoLabel}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12">
                <div class="row">
                  <div class="col-6">
                    <label for="origen">Origen</label>
                    <input
                      disabled
                      :value="route.origen"
                      type="text"
                      class="form-control"
                      placeholder="Agregue Origen"
                    />
                  </div>
                  <div class="col-6">
                    <label for="destino">Destino</label>
                    <input
                      disabled
                      :value="route.destino"
                      type="text"
                      class="form-control"
                      placeholder="Agregue Destino"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="row mt-4">
              <div class="col-12">
                <div class="row">
                  <div class="col-3">
                    <label for>Fecha de Carga</label>
                    <date-picker
                      disabled
                      @change="changeDate($event)"
                      format="DD-MM-YYYY   HH:mm"
                      v-model="route.fecha"
                      type="datetime"
                      lang="lang"
                    ></date-picker>
                  </div>
                  <div class="col-3">
                    <label for>Tiempo</label>
                    <input disabled :value="timeToDisplay" type="text" class="form-control" />
                  </div>
                  <div class="col-3">
                    <label for>Kms</label>
                    <input disabled :value="distanceToDisplay" type="text" class="form-control" />
                  </div>
                  <div class="col-3">
                    <label for>Casetas</label>
                    <input disabled :value="route.casetas" type="text" class="form-control" id />
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12 mt-4">
                <a
                  target="_blank"
                  :href="'https://www.google.com/maps/dir/?api=1&travelmode=driving&origin='+encodedOrigen+'&destination='+encodedDestino"
                >
                  <button class="btn btn-primary">Ver Indicaciones</button>
                </a>
              </div>
            </div>
            <div class="row mt-4">
              <div class="col-12">
                <div style="widtth:100%; height:500px;" :id="'map'+index"></div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="loading" class="text-right loading_shadow">
      <div class="row justify-content-end">
        <div class="mr-2 spinner-border" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <p class="pt-1 ml-2">Cargando...</p>
      </div>
    </div>
  </div>
</template>

<script>
import DatePicker from "vue2-datepicker";

export default {
  props: {
    index: Number,
    max: Number,
    isRoundTrip: Boolean,
    route: Object,
    previousRoute: Object,
    ejes: String,
    tollGurukey: Object
  },
  data() {
    return {
      loading: false,
      polyline: "",
      casetas: null
    };
  },
  created() {
    //Set Current DateTime on Date Field
    this.route.fecha = new Date();
  },
  mounted() {
    //Set autocompletes for Origin and Destination
    this.setAutoCompleteFields();
    this.checkForOriginAndDesitination();
    this.nuevo_mapa = new google.maps.Map(
      document.getElementById("map" + this.index),
      {
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoom: 4,
        center: { lat: 23.85, lng: -102.65 }
      }
    );
  },
  watch: {
    route: {
      deep: true,
      handler: function(val) {
        this.emitRoute();
      }
    },
    calculateMinDate: {
      handler: function(val) {
        var self = this;
        console.log(this.route.fecha);
        if (Date.parse(this.route.fecha) < Date.parse(val)) {
          this.route.fecha = val;
        }
      }
    },
    ejes: {
      handler: function(val) {
        if (this.route.polyline != "") {
          this.calcTolls();
        }
      }
    },
    previousDestination: {
      handler: function(val) {
        this.route.origen = val;
      }
    },
    "route.origen": {
      handler: function(val) {
        this.checkForOriginAndDesitination();
      }
    },
    "route.destino": {
      handler: function(val) {
        this.checkForOriginAndDesitination();
      }
    }
  },
  computed: {
    tipoTramo: function() {
      var message = "";
      if (this.index == 0) {
        message = "inicial";
      }
      if (this.index > 0 && this.index != this.max - 1) {
        message = "default";
      }
      if (this.index == this.max - 1) {
        if (this.isRoundTrip) {
          message = "final";
        } else {
          message = "default";
        }
      }
      return message;
    },
    tramoLabel: function() {
      var tramoLabel = "";
      switch (this.tipoTramo) {
        case "inicial":
          tramoLabel = "Tramo Inicial";
          break;
        case "default":
          tramoLabel = "Tramo " + this.index;
          break;
        case "final":
          tramoLabel = "Tramo Final";
          break;
      }
      return tramoLabel;
    },
    fechaLabel: function() {
      var fechaLabel = "";
      switch (this.tipoTramo) {
        case "inicial":
          fechaLabel = "Fecha de Carga";
          break;
        case "default":
          fechaLabel = "Fecha de Entrega";
          break;
        case "final":
          fechaLabel = "Fecha de Disponibilidad";
          break;
      }
      return fechaLabel;
    },
    previousDestination: function() {
      try {
        this.route.origen = this.previousRoute.destino;
        return this.previousRoute.destino;
      } catch (error) {
        this.route.origen = "";
        return "";
      }
    },
    calculateMinDate: function() {
      if (this.previousRoute != null && this.previousRoute.fecha != "") {
        var calc_date = new Date(
          Date.parse(this.previousRoute.fecha) +
            this.getTimeInMillisecondsFromDistance() +
            this.previousRoute.load_time * 3600000
        );
        this.route.fecha = calc_date;
        return calc_date;
      } else {
        return new Date();
      }
    },
    timeToDisplay: function() {
      var distance = this.route.distancia || 0;
      var distance_in_kms = distance / 1000;
      var hours_at_60kms = distance_in_kms / 60;
      var minutes_at_60kms = (distance_in_kms % 60) * 60;
      var time =
        Math.floor(hours_at_60kms) +
        "h " +
        Math.floor(distance_in_kms % 60) +
        "m ";
      return time;
    },
    timeToDisplayNumber: function() {
      return (this.route.distancia / 1000 / 60) * 3.6e6;
    },
    distanceToDisplay: function() {
      return this.route.distancia != 0
        ? Math.round(this.route.distancia / 1000) + " km"
        : "0 km";
    },
    encodedOrigen: function() {
      return encodeURI(this.route.origen);
    },
    encodedDestino: function() {
      return encodeURI(this.route.destino);
    }
  },
  methods: {
    deleteTramo() {
      this.$emit("tramoDeleted", this.index);
    },
    deleteTramo() {
      this.$emit("emitCasetas", this.index);
    },
    getTimeInMillisecondsFromDistance() {
      var distance = this.route.distancia || 0;
      var distance_in_kms = distance / 1000;
      var hours = Math.floor(distance_in_kms / 60);
      var minutes = Math.floor(distance_in_kms % 60);
      var timeInMillseconds = hours * 3.6e6 + minutes * 60000;
      return timeInMillseconds;
    },
    changeDate(fecha) {
      this.route.fecha = fecha;
    },
    changeHours(event) {
      this.route.load_time = event.target.value;
    },
    emitRoute() {
      this.$emit("routeUpdated", JSON.parse(JSON.stringify(this.route)));
    },
    checkForOriginAndDesitination() {
      if (this.route.origen != "" && this.route.destino != "") {
        this.calcRoute();
      } else {
        this.route.casetas = 0;
        this.route.tiempo = "";
        this.route.distancia = "";
        this.route.casetas = null;
        this.route.desgloze_casetas = null;
      }
    },
    createMap() {
      this.nuevo_mapa.setZoom(6);
    },
    calcRoute() {
      this.loading = true;

      var directionsService = new google.maps.DirectionsService();
      var directionsRenderer = new google.maps.DirectionsRenderer({
        map: this.nuevo_mapa,
        draggable: true
      });

      var request = {
        origin: this.route.origen,
        destination: this.route.destino,
        waypoints: JSON.parse(this.route.polyline),
        travelMode: google.maps.DirectionsTravelMode.DRIVING
      };

      //Instance this to self in order to use it inside the callback function
      var self = this;
      var waypoints = [];

      directionsRenderer.addListener("directions_changed", function() {
        var result = directionsRenderer.getDirections();

        console.log(result);
        //Set values after calculate route
        var legs = result.routes[0].legs[0];

        self.route.tiempo = legs.duration.value;
        self.route.distancia = legs.distance.value;
        self.route.distanceToDisplay = legs.distance.text;

        self.polyline = self.calculatePolyline(result);

        var via_waypoints = legs.via_waypoints;

        for (var i = 0; i < via_waypoints.length; i++) {
          var waypoint = {
            location: {
              lat: parseInt(via_waypoints[i].lat()),
              lng: parseInt(via_waypoints[i].lng())
            },
            stopover: false
          };
          waypoints.push(waypoint);
        }

        if (via_waypoints.length > 0) {
          self.route.polyline = JSON.stringify(waypoints);
        }

        // Calcular costo de casetas
        self.calcTolls();
      });

      directionsService.route(request, function(result, status) {
        if (status == "OK") {
          directionsRenderer.setDirections(result);
        }
      });
    },
    calcTolls() {
      this.route.casetas = 0;
      var data = {
        vehicleType: this.ejes,
        polyline: this.polyline,
        source: "gmaps"
      };

      var self = this;
      fetch("https://dev.tollguru.com/v1/calc/route", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
          "x-api-key": this.tollGurukey.keytoll
        }
      })
        .then(res => res.json())
        .then(function(data) {
          if (data.status != "ERROR") {
            //Add +1 to Use Counter
            fetch(
              "http://www.misistema.mx/beluga/Finanzas/endpoints/post/useKey.php",
              {
                method: "POST",
                body: JSON.stringify({ idKey: self.tollGurukey.idKey }),
                headers: {
                  "Content-type": "text/plain;charset=UTF-8"
                }
              }
            );

            var casetas = [];

            for (var i = 0; i < data.route.tolls.length; i++) {
              self.route.casetas += data.route.tolls[i].cashCost;

              var caseta = {
                cashCost: data.route.tolls[i].cashCost,
                name: data.route.tolls[i].name
              };
              casetas.push(caseta);
            }

            self.route.desgloze_casetas = JSON.parse(JSON.stringify(casetas));
          } else {
            Swal.fire({
              type: "error",
              title: "Error",
              text: "Revisar llave de Toll Guru"
            });
          }
          self.loading = false;
        });
    },
    calculatePolyline(result) {
      var polyline = new google.maps.Polyline({
        path: []
      });
      var legs = result.routes[0].legs;
      for (var i = 0; i < legs.length; i++) {
        var steps = legs[i].steps;
        for (var j = 0; j < steps.length; j++) {
          var nextSegment = steps[j].path;
          for (var k = 0; k < nextSegment.length; k++) {
            polyline.getPath().push(nextSegment[k]);
          }
        }
      }
      var encoded_polyline = google.maps.geometry.encoding.encodePath(
        polyline.getPath()
      );

      return encoded_polyline;
    },
    decodePolyline(encoded_polyline) {
      var decoded_polyline = new google.maps.geometry.encoding.decodePath(
        encoded_polyline
      );
      return decoded_polyline;
    },
    setAutoCompleteFields() {
      this.google_autocomplete_origen = new google.maps.places.Autocomplete(
        this.$refs.autocomplete_origin
      );
      this.google_autocomplete_origen.addListener("place_changed", () => {
        this.route.origen = this.google_autocomplete_origen.getPlace().formatted_address;
      });

      this.google_autocomplete_destino = new google.maps.places.Autocomplete(
        this.$refs.autocomplete_destino
      );
      this.google_autocomplete_destino.addListener("place_changed", () => {
        this.route.destino = this.google_autocomplete_destino.getPlace().formatted_address;
      });
    }
  },
  components: {
    DatePicker
  }
};
</script>
