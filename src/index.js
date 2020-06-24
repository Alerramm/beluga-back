const express = require('express');
const morgan = require('morgan');
const app = express();
// const mongoose = require('mongoose');

// mongoose.connect('mongodb://localhost/beluga-database')
//     .then(db => console.log("DB is connected"))
//     .catch(err => console.error(err));

//Settings
app.set('port', process.env.PORT || 3000);

//Middleware
app.use(morgan('dev'));
app.use(express.json());

//Routes
app.use('/tasks',require('./routes/tasks'));

//Static files
app.use(express.static(__dirname + '/public'));


app.listen(app.get('port'),function () {
    console.log("Server on port", app.get('port'));
});