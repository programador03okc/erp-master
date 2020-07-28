const express = require("express");
const socketio = require("socket.io");
const bodyParser = require("body-parser");
const http = require("http");
const app = express();

var server = http.createServer(app);
const io = socketio(server);

const clients = [];

app.use(
    express.urlencoded({
        extended: true
    })
);

/**
 * Initialize Server
 */
server.listen(8008, function() {
    console.log("Servidor corriendo en puerto 8008");
});

/**
 * Página de Teste
 */
app.get("/", function(req, res) {
    res.send("Servidor corriendo...");
});

// Recebe requisição do Laravel
app.post("/notification", function(req, res) {
    var params = req.body;
    var clients = io.sockets.clients().sockets;

    for (const key in clients) {
        if (key != params.id) clients[key].emit("notification", params);
    }

    res.send();
});

app.post("/notificaciones_sin_leer", function(req, res) {
    var params = req.body;
    var clients = io.sockets.clients().sockets;

    for (const key in clients) {
        if (key != params.id) clients[key].emit("notificaciones_sin_leer", params);
    }

    res.send();
});

// Recebe conexão dos usuários no servidor
io.on("connection", function(client) {
    // Adicionado clientes
    client.emit("welcome", {
        id: client.id
    });
});