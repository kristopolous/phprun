var php =(function(){
  var 
    endpoint = '/phprun/endpoint.php',
    isAuthed;

  function ajax(func, payload, cb) {
    var request = new XMLHttpRequest();
    request.open('POST', endpoint, true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.onload = function(){
       if (request.status >= 200 && request.status < 400){
         var res;
         try {
           res = JSON.parse(request.responseText);
         } catch(ex) {}

         cb(res || {res: false});
       } else { cb({res: false}) };
    }
    request.onerror = function() { cb({res: false}); };
    request.send(JSON.stringify({func: func, data: payload}));
  }

  
  function run(cmd) {
    if (isAuthed) {
      return ajax('run', cmd, function(res){
        console.log(res.data || 'Code not successfully run.');
      });
    } 
    console.log('Not authorized');
  }

  return function (password) {
    if(isAuthed) { return run(password); }

    ajax('login', password, function(res) {
      isAuthed = res.res;
      console.log('Authorization ' + ['un',''][+isAuthed] + 'successful.');
    });

    return run;
  }
})();

