var ltr = false;
var rtl = false;
var mixed = false;
var study = false;
var test = false;

var current_word = "";
var current_translation = "";
var finished = false;
var words = [];

function init() {
    var id = urlParam("id");

    if (id == null) {
        console.log("not found");
    } else if(Math.floor(id) != id && !$.isNumeric(id)) {
        console.log("not found 2");
    } else {
        var url = "/list/json.php?id=" + id;

        $.getJSON(url, function(data) {
            switch(urlParam("a")) {
                case "study":
                    study = true;
                    break;
                case "test":
                    test = true;
                    break;
                default:
                    study = true;
                    break;
            }

            switch(urlParam("b")) {
                case "ltr":
                    ltr = true;
                    $("#translate_to").text("Translate to " + data.language_2);
                    break;
                case "rtl":
                    rtl = true;
                    $("#translate_to").text("Translate to " + data.language_1);
                    break;
                case "mixed":
                    mixed = true;
                default:
                    ltr;
                    break;
            }
            words = data.words;
            $("#container").show();


            run(data);
        });
    }
}

function run(data) {
    var pair = words[getRandomInt(0, words.length - 1)];

    if (ltr) {
        current_word = pair.word1;
        current_translation = pair.word2;
    } else if (rtl) {
        current_word = pair.word2;
        current_translation = pair.word1;
    } else if (mixed) {
        var random_boolean = Math.random() >= 0.5;

        if (random_boolean) {
            current_word = pair.word1;
            current_translation = pair.word2;
            $("#translate_to").text("Translate to " + data.language_2);
        } else {
            current_word = pair.word2;
            current_translation = pair.word1;
            $("#translate_to").text("Translate to " + data.language_1);
        }
    }

    $("#word1").text(current_word);
}

$("#check_button").click(function() {
    var entered = $("")
});

function urlParam(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
       return null;
    }
    else{
       return decodeURI(results[1]) || 0;
    }
}

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

$(document).ready(function() {
    init();
});
