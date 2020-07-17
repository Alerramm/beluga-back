<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Codigos Error</title>
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

</head>
<body>
<h1>Codigos Error </h1>
<div id='vueapp'>

<table border='1' width='100%' style='border-collapse: collapse;'>
   <tr>
     <th>Codigo</th>
     <th>Descripcion</th>
   </tr>

   <tr v-for='codigo in codigos'>
     <td>{{ codigo.clave }}</td>
     <td>{{ codigo.descripcion }}</td>

   </tr>
 </table>
</br>

    <form>
      <label>Codigo</label>
      <input type="text" name="clave" v-model="clave">
</br>
     
      <input type="button" @click="createCodigo()" value="busqueda">
    </form>

</div>
<style>
    input {
  width: 100%;
  padding: 2px 5px;
  margin: 2px 0;
  border: 1px solid red;
  border-radius: 4px;
  box-sizing: border-box;
}

input[type=button]{
  background-color: #4CAF50;
  border: none;
  color: white;
  padding: 4px 7px;
  text-decoration: none;
  margin: 2px 1px;
  cursor: pointer;
}
th, td {
  padding: 1px;
  text-align: left;
  border-bottom: 1px solid #ddd;
  
}
tr:hover {background-color: #f5f5f5;}

</style>

<script>
   getCodigos: function(){
        axios.get('endpoints/get/codigos.php')
        .then(function (response) {
            console.log(response.data);
            app.codigos = response.data;

        })
        .catch(function (error) {
            console.log(error);
        });
    }

</script>
	
<script>	
	
var app = new Vue({
  el: '#vueapp',
  data: {
      clave: '',
      descripcion: '',
    codigos: []
  },
  mounted: function () {
    console.log('Hello from Vue!')
    this.getCodigos()
  },

  methods: {
    getCodigos: function(){
    }
  }
})    
</script>
</body>
</html>