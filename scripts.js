var answers = {};

var question_one = document.getElementById('question-1');
var question_two = document.getElementById('question-2');
var question_three = document.getElementById('question-3');
var question_four = document.getElementById('question-4');
var question_five = document.getElementById('question-5');
var question_six = document.getElementById('question-6');
var question_seven = document.getElementById('question-7');
var question_eight = document.getElementById('question-8');
var question_nine = document.getElementById('question-9');
var question_ten = document.getElementById('question-10');
var question_eleven = document.getElementById('question-11');
var question_twelve = document.getElementById('question-12');
var question_thirteen = document.getElementById('question-13');
var question_fourteen = document.getElementById('question-14');
var question_fifteen = document.getElementById('question-15');


function storeAnswer(question_number, event) {
    if (event.target.type === 'radio') {
        console.log(event.target.value);
        answers['question' + question_number] = parseInt(event.target.value);
        console.log(answers);
    }
}

question_one.addEventListener('click', function (event) {
    storeAnswer(1, event)
})
question_two.addEventListener('click', function (event) {
    storeAnswer(2, event)
})
question_three.addEventListener('click', function (event) {
    storeAnswer(3, event)
})
question_four.addEventListener('click', function (event) {
    storeAnswer(4, event)
})
question_five.addEventListener('click', function (event) {
    storeAnswer(5, event)
})
question_six.addEventListener('click', function (event) {
    storeAnswer(6, event)
})
question_seven.addEventListener('click', function (event) {
    storeAnswer(7, event)
})

question_eight.addEventListener('click', function (event) {
    storeAnswer(8, event)
})
question_nine.addEventListener('click', function (event) {
    storeAnswer(9, event)
})

question_ten.addEventListener('click', function (event) {
    storeAnswer(10, event)
})

question_eleven.addEventListener('click', function (event) {
    storeAnswer(11, event)
})

question_twelve.addEventListener('click', function (event) {
    storeAnswer(12, event)
})
question_thirteen.addEventListener('click', function (event) {
    storeAnswer(13, event)
})
question_fourteen.addEventListener('click', function (event) {
    storeAnswer(14, event)
})
question_fifteen.addEventListener('click', function (event) {
    storeAnswer(15, event)
})

function totalScore() {
    var total_scoreA =
        answers.question1 +
        answers.question2 +
        answers.question3 +
        answers.question4 +
        answers.question5 +
        answers.question6 +
        answers.question7 +
        answers.question8 +
        answers.question9 +
        answers.question10;

    var total_scoreB =
        answers.question11 +
        answers.question12 +
        answers.question13 +
        answers.question14 +
        answers.question15;
    var total_score = total_scoreA + (total_scoreB * 2)


    return total_score;
}



function getInfoBasedOnScore() {


    if (totalScore() <= 90) {
        var score_info = "É improvável que você tenha Pouco Desejo Sexual (PDS)";
    } if (totalScore() >= 91) {
        var score_info = "Aumenta a possibilidade do Pouco Desejo Sexual(PDS) estar presente, mas isso não pode ser claramente determinado a partir de suas respostas.";
     } if (totalScore() >= 121) {
        var score_info = "Resultado sugere muito a presença do Pouco Desejo Sexual(PDS), mas de forma alguma a provam.";
     } if(totalScore() > 140) {
        var score_info = "Você tem Pouco Desejo Sexual (PDS), embora não possa ser feito um diagnóstico definitivo com base apenas em um teste online.";   
        
    } 


    return score_info;
}

var submit1 = document.getElementById('submit1');
var submit2 = document.getElementById('submit2');
var submit3 = document.getElementById('submit3');
var submit4 = document.getElementById('submit4');
var submit5 = document.getElementById('submit5');
var submit6 = document.getElementById('submit6');
var submit7 = document.getElementById('submit7');
var submit8 = document.getElementById('submit8');
var submit9 = document.getElementById('submit9');
var submit10 = document.getElementById('submit10');
var submit11 = document.getElementById('submit11');
var submit12 = document.getElementById('submit12');
var submit13 = document.getElementById('submit13');
var submit14 = document.getElementById('submit14');
var submit15 = document.getElementById('submit15');


function nextQuestion(question_number) {
    var current_question_number = question_number - 1;
    var question_number = question_number.toString();
    var current_question_number = current_question_number.toString();

    var el = document.getElementById('question-' + question_number);
    var el2 = document.getElementById('question-' + current_question_number);

    el.style.display = "block";
    el2.style.display = "none";
}

submit1.addEventListener('click', function () {
    nextQuestion(2);
    growProgressBar('13.34%');
})

submit2.addEventListener('click', function () {
    nextQuestion(3);
    growProgressBar('20.01%');
})

submit3.addEventListener('click', function () {
    nextQuestion(4);
    growProgressBar('26.68%');
})

submit4.addEventListener('click', function () {
    nextQuestion(5);
    growProgressBar('33.35%');
})

submit5.addEventListener('click', function () {
    nextQuestion(6);
    growProgressBar('40.00%');
})

submit6.addEventListener('click', function () {
    nextQuestion(7);
    growProgressBar('46.69%');
})

submit7.addEventListener('click', function () {
    nextQuestion(8);
    growProgressBar('53.36%');
})

submit8.addEventListener('click', function () {
    nextQuestion(9);
    growProgressBar('60.00%');
})

submit9.addEventListener('click', function () {
    nextQuestion(10);
    growProgressBar('.66.70%');
})

submit10.addEventListener('click', function () {
    nextQuestion(11);
    growProgressBar('73.37%');
})

submit11.addEventListener('click', function () {
    nextQuestion(12);
    growProgressBar('80.00%');
})

submit12.addEventListener('click', function () {
    nextQuestion(13);
    growProgressBar('86.71%');
})

submit13.addEventListener('click', function () {
    nextQuestion(14);
    growProgressBar('93.38%');
})

submit14.addEventListener('click', function () {
    nextQuestion(15);
    growProgressBar('100%');

})

submit15.addEventListener('click', function () {
    nextQuestion(16);
})

submit15.addEventListener('click', function () {
    document.getElementById("printtotalscore").innerHTML = totalScore();
    document.getElementById("printscoreinfo").innerHTML = getInfoBasedOnScore();
})



function growProgressBar(percentage_width) {
    var bar = document.getElementById("progress_bar");
    bar.style.width = percentage_width;
}

