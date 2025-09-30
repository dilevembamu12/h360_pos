
const doc_id = "h360_1_1";
DATA={};
const db = new PouchDB('h360_1');
//const db = new PouchDB('http://admin:admin@localhost:5984/h360_1');

const remoteDB = new PouchDB('http://admin:admin@localhost:5984/h360_1');


function newItem() {
    var item = document.getElementById('name').value;
    console.log("new item: " + item);

    var now = new Date;
    var doc = {
        "_id": String(now.getTime()),
        "item": item
    };

    console.log(doc);
    db.put(doc).then(function () {
        return getItemList()
    }).then(function (contents) {
        document.getElementById('itemList').innerHTML = contents;
    }).catch(function (err) {
        console.log("OOOOPS");
        console.log(err);
    });

    document.getElementById('name').value = '';
    // don't actually submit the HTML form
    return false;
}

function deleteItem(id, rev) {
    db.remove(id, rev).then(function (result) {
        return getItemList()
    }).then(function (contents) {
        document.getElementById('itemList').innerHTML = contents;
    }).catch(function (err) {
        console.log("Uh oh");
        console.log(err);
    });
    return false;
}

function consoleDATA(){
    console.log('DATA',DATA);
}

function getItemList() {
    return new Promise(function (resolve, reject) {
        document.title="H360 POS LITE - "+doc.business.name;
        var formattedList = '<ul>';

        db.get(doc_id).then(function (doc) {
            

            DATA=doc;
            formattedList += "<li onclick=\"consoleDATA()\">" + doc.business.name + " &nbsp; <a href=\"#\" onclick=\"deleteItem('" + doc._id + "', '" + doc._rev + "');\">x</a></li>";

            formattedList += '</ul>';

            // console.log(formattedList);
            resolve(formattedList);
        }).catch(function (err) {
            console.log(err);
        });

        /*
      db.allDocs({include_docs: true, descending: true}).then(function (response) {
          


          response.rows.forEach(function (row) {
              console.log(row);
              formattedList += "<li>" + row.doc.business.name + " &nbsp; <a href=\"#\" onclick=\"deleteItem('" + row.doc._id + "', '" + row.doc._rev + "');\">x</a></li>";
          });
          formattedList += '</ul>';

          // console.log(formattedList);
          resolve(formattedList);
      }).catch(function (err) {
          console.log("UH OH");
          console.log(err);
      });
      */
    });
}

window.onload = function () {

    //document.getElementById('newItem').onsubmit = newItem;

    db.sync(remoteDB, { live: true, retry: true }
        
    ).on('change', function (change) {
        
        return getItemList().then(function (contents) {
            document.getElementById('itemList').innerHTML = contents;
        })
    }).on('active', function (info) {
        //alert(465464456);
        
        return getItemList().then(function (contents) {
            document.getElementById('itemList').innerHTML = contents;
        });
        
    }).on('complete', function (info) {
        //alert(465464456);
        
        return getItemList().then(function (contents) {
            document.getElementById('itemList').innerHTML = contents;
        });
        
    });
    
    getItemList().then(function (contents) {
        document.getElementById('itemList').innerHTML = contents;
    });
    alert(755156);
    console.log("loaded");



}

