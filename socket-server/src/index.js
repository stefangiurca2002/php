const app = require('express')();
const server = app.listen(4444);
const io = require('socket.io')(server);
var mysql = require('mysql'),
//request = require('request'),
bodyParser = require('body-parser');
var FCM = require('fcm-node');
var serverKey = 'AAAAeNSeOQw:APA91bHmcfWv9A94iibumkrbYa5ROJG3HgVOzURIj462n6517u-GpCb5oyMAO9-f9cPpz5W4GMKPM7UFVqJEYPDVv3utJWmbFSKKPNNPgpD4oJRq6s3nFsDpZESyZiL34qzPVrh2coa4'; //put your server key here
var fcm = new FCM(serverKey);
var users = [];
var con = mysql.createConnection({
    host: "localhost",
    port: "3306",
    user: "stefan",
    password: "stefan1joc",
    database: 'mediaApp'
  });
  
  con.connect(function(err) {
    if (err) throw err;
    console.log("Connected to mysql");
  });

  app.use(bodyParser.json());
  app.use(bodyParser.urlencoded({
      extended: false
  }));

app.use(function(req, res, next) {
    res.header("Access-Control-Allow-Credentials", true);
    res.header("Access-Control-Allow-Origin", "localhost:8100");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
  });

app.get('/', function(req, res){
    res.send('welcome to express');
});


io.on("connection", socket => {
   socket.on('inApp',(user) => {
       var code = user.code;
       console.log(user);
       var sql = `SELECT userName FROM users WHERE code='${code}'`;
       con.query(sql, (err, res)=>{
            if(err) throw err;
            else{
                  users.push({
                     userName: res[0].userName,
                     socket: socket.id
                  });
                  console.log(users);
            }
       })
   })
   socket.on('emitMessage', data =>{
       console.log(data);
    var code = data.code;
    var sql = `SELECT userName FROM users WHERE code='${code}'`;
    con.query(sql, (err, res)=>{
         if(err) throw err;
         else{
            let i, ok = true;
             for(i=0; i<users.length; i++){
                 if(users[i].userName == data.user){
                     ok = false;
                     io.to(users[i].socket).emit('NewMessage',{
                         from: res[0].userName,
                         msg: data.msg,
                         date: data.date
                     });
                 }
             }
             if(ok){
                 user = data.user;
               con.query(`SELECT token FROM divaces WHERE userName='${user}'`,(err, result)=>{
                   if(err) throw err;
                   else{
                       for(let i=0; i<result.length; i++){
                        var message = { 
                            // this may vary according to the message type (single
                            // recipient, multicast, topic, et cetera)
                                
                                to: result[i].token,
                                collapse_key: 'your_collapse_key',
                    
                                notification: {
                                    title: `message from: ${res[0].userName}`,
                                    body: data.msg
                                },
                    
                                data: {  
                                // you can send only notification or only 
                                // data(or include both)
                                    my_key: 'my value',
                                    my_another_key: 'my another value'
                                }
                            };
                    
                            fcm.send(message, function (err, response) {
                                if (err) {
                                    console.log("Something has gone wrong!",
                                    result[i].token,
                                    userName);
                                } else {
                                    console.log("Successfully sent with response: ", response);
                                }
                            });
                        console.log('send msg', result);                
                       }
                   }
               })
             }
         }
    })
   })
   socket.on('disconnect', ()=>{
    console.log(socket.id, 'disconnected');
    for(let i=0; i<users.length; i++){
        if(users[i].socket == socket.id){
            for(let j=i; j<users.length-1; j++){
                users[j]=users[j+1];
            }
            users.pop();
            break;
        }
    }
})
})