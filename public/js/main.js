monCanvas = document.getElementById("monCanvas");

monCanvas.width = 500;
monCanvas.height = 500;
const MAX = 90;
const MIN = 20;

const ctx = monCanvas.getContext('2d');

ctx.clearRect(50, 50, monCanvas.width, monCanvas.height);

//*********************************  CREER COORDONEES ALEATOIRES *******************************
function aleatoire(MIN,MAX){
    return Math.floor(Math.random() * (MAX - MIN + 1)) * 5;
}
let aleatoireMickeyX = aleatoire(MIN,MAX);
let aleatoireMickeyY = aleatoire(MIN,MAX);
let aleatoireMechant1X = aleatoire(MIN,MAX);
let aleatoireMechant1Y = aleatoire(MIN,MAX);
let aleatoireMechant2X = aleatoire(MIN,MAX);
let aleatoireMechant2Y = aleatoire(MIN,MAX);

//********************************  CREER MECHANTS ***************************************
let mechant1 = new Image(20,20);
mechant1.src = "../image/mechant.gif"
mechant1.onload =() => {
    return ctx.drawImage(mechant1, aleatoireMechant1X, aleatoireMechant1Y)
}

let mechant2 = new Image(20,20);
mechant2.src = "../image/mechant.gif"
mechant2.onload =() => {
    return ctx.drawImage(mechant2, aleatoireMechant2X, aleatoireMechant2Y)
}

//******************************** CREER MICKEY ******************************************
let mickey = new Image(20,20);
mickey.src = "../image/mickey.png"
mickey.onload =() =>{
    return ctx.drawImage(mickey, aleatoireMickeyX, aleatoireMickeyY)
}

//********************************  CREER SARAH  *****************************************
let sarah1 = new Image(50, 50);
sarah1.src = "../image/sss.gif";

let sarah2 = new Image(50, 50);
sarah2.src = "../image/SarahBis.gif";
sarah2.onload = () => {
    return ctx.drawImage(sarah2, 20, 20);
}

let affiche = false;
function sarah(){
    if(!affiche){
        affiche = true;
        return sarah1;
    } else {
        affiche = false;
        return sarah2;
    }
}

//********************************  CREER TROUVE  ****************************************
function trouve(x,y,aleaX,aleaY){
    maxX = aleaX+20;
    maxY = aleaY+20;
    minX = aleaX-20;
    minY = aleaY-20;
    if((x>=minX && x<=maxX) && (y>=minY && y<=maxY)){
        return gagne();
    }
}
function gagne(){
    window.alert("Approche !");
    window.location.href="../jeu/victoire.html";
}

//******************************** CREER PERDU  ******************************************
function perdu(mechantX,mechantY,x,y){
    maxX = mechantX+20;
    maxY = mechantY+20;
    if((x>=mechantX && x<=maxX) && (y>=mechantY && y<=maxY)) {
        return loose();
    }
}
function loose(){
    window.alert("T'as perdu biatch !")
    window.location.href="../jeu/toucher.html";
}

// MOUVEMENT
let x= 20;
let y = 20;

let valeurY =20;
let valeurX =20;

//********************************* DEPLACEMENT MECHANTS  *********************************************

function deplacementMechant(){
    let valeurXMechant = aleatoireMechant1X;
    let valeurYMechant = aleatoireMechant1Y;

    let direction = (Math.floor(Math.random() * 4));

    switch(direction){
        case 0 : ctx.clearRect(aleatoireMechant1X, aleatoireMechant1Y, 20,20);
            if (aleatoireMechant1X < 450) {
                aleatoireMechant1X += 5;
            } else {
                aleatoireMechant1X = 450;
            }
            aleatoireMechant1Y = valeurYMechant;
            ctx.drawImage(mechant1, aleatoireMechant1X, aleatoireMechant1Y);
            valeurYMechant = aleatoireMechant1Y;
            valeurXMechant = aleatoireMechant1X; break;


        case 1 : ctx.clearRect(aleatoireMechant1X, aleatoireMechant1Y, 20,20);
            if (aleatoireMechant1X > 0) {
                aleatoireMechant1X -= 5;
            } else {
                aleatoireMechant1X = 0;
            }
            aleatoireMechant1Y = valeurYMechant;
            ctx.drawImage(mechant1, aleatoireMechant1X, aleatoireMechant1Y);
            valeurYMechant = aleatoireMechant1Y;
            valeurXMechant = aleatoireMechant1X; break;

        case 2 : ctx.clearRect(aleatoireMechant1X, aleatoireMechant1Y, 20,20);
            if (aleatoireMechant1Y < 450) {
                aleatoireMechant1Y += 5;
            } else {
                aleatoireMechant1Y = 450;
            }
            aleatoireMechant1X = valeurXMechant;
            ctx.drawImage(mechant1, aleatoireMechant1X, aleatoireMechant1Y);
            valeurYMechant = aleatoireMechant1Y;
            valeurXMechant = aleatoireMechant1X;break;

        case 3 : ctx.clearRect(aleatoireMechant1X, aleatoireMechant1Y, 20,20);
            if (aleatoireMechant1Y > 0) {
                aleatoireMechant1Y -= 5;
            } else {
                aleatoireMechant1Y = 0;
            }
            aleatoireMechant1X = valeurXMechant;
            ctx.drawImage(mechant1, aleatoireMechant1X, aleatoireMechant1Y);
            valeurYMechant = aleatoireMechant1Y;
            valeurXMechant = aleatoireMechant1X;break;
    }
    perdu(aleatoireMechant1X,aleatoireMechant1Y,aleatoireMickeyX, aleatoireMickeyY);
}

function deplacementMechant2(){
    let valeurXMechant = aleatoireMechant2X;
    let valeurYMechant = aleatoireMechant2Y;

    let direction = (Math.floor(Math.random() * 4));

    switch(direction){
        case 0 : ctx.clearRect(aleatoireMechant2X, aleatoireMechant2Y, 20,20);
            if (aleatoireMechant2X < 450) {
                aleatoireMechant2X += 5;
            } else {
                aleatoireMechant2X = 450;
            }
            aleatoireMechant2Y = valeurYMechant;
            ctx.drawImage(mechant2, aleatoireMechant2X, aleatoireMechant2Y);
            valeurYMechant = aleatoireMechant2Y;
            valeurXMechant = aleatoireMechant2X; break;

        case 1 : ctx.clearRect(aleatoireMechant2X, aleatoireMechant2Y, 20,20);
            if (aleatoireMechant2X > 0) {
                aleatoireMechant2X -= 5;
            } else {
                aleatoireMechant2X = 0;
            }
            aleatoireMechant2Y = valeurYMechant;
            ctx.drawImage(mechant2, aleatoireMechant2X, aleatoireMechant2Y);
            valeurYMechant = aleatoireMechant2Y;
            valeurXMechant = aleatoireMechant2X; break;

        case 2 : ctx.clearRect(aleatoireMechant2X, aleatoireMechant2Y, 20,20);
            if (aleatoireMechant2Y < 450) {
                aleatoireMechant2Y += 5;
            } else {
                aleatoireMechant2Y = 450;
            }
            aleatoireMechant2X = valeurXMechant;
            ctx.drawImage(mechant2, aleatoireMechant2X, aleatoireMechant2Y);
            valeurYMechant = aleatoireMechant2Y;
            valeurXMechant = aleatoireMechant2X;break;

        case 3 : ctx.clearRect(aleatoireMechant2X, aleatoireMechant2Y, 20,20);
            if (aleatoireMechant2Y > 0) {
                aleatoireMechant2Y -= 5;
            } else {
                aleatoireMechant2Y = 0;
            }
            aleatoireMechant2X = valeurXMechant;
            ctx.drawImage(mechant2, aleatoireMechant2X, aleatoireMechant2Y);
            valeurYMechant = aleatoireMechant2Y;
            valeurXMechant = aleatoireMechant2X;break;
    }
    perdu(aleatoireMechant2X,aleatoireMechant2Y,aleatoireMickeyX, aleatoireMickeyY);
}

// ***********************************     DEPLACEMENT SARAH   ***********************************************
function deplacerSarahDroite() {
    ctx.clearRect(x, y, 50, 50);
    if (x < 450) {
        x += 5;
    } else {
        x = 450;
    }
    y = valeurY;
    ctx.drawImage(sarah(), x, y);
    valeurY = y;
    valeurX = x;
    trouve(valeurX,valeurY,aleatoireMickeyX, aleatoireMickeyY);
    perdu(valeurX, valeurY,aleatoireMechant1X,aleatoireMechant1Y);
    perdu(valeurX,valeurY,aleatoireMechant2X, aleatoireMechant2Y);
    console.log(valeurX, valeurY);
}

function deplacerSarahGauche() {
    ctx.clearRect(x, y, 50, 50);
    if (x > 0) {
        x -= 5;
    } else {
        x = 0
    }
    y = valeurY;
    ctx.drawImage(sarah(), x, y);
    valeurY = y;
    valeurX = x;
    trouve(valeurX,valeurY,aleatoireMickeyX, aleatoireMickeyY);
    perdu(valeurX, valeurY,aleatoireMechant1X,aleatoireMechant1Y);
    perdu(valeurX,valeurY,aleatoireMechant2X, aleatoireMechant2Y);
    console.log(valeurX, valeurY);
}

function deplacerSarahHaut() {
    ctx.clearRect(x, y, 50, 50);
    x = valeurX;
    if (y > 0) {
        y -= 5;
    } else {
        y = 0;
    }
    ctx.drawImage(sarah(), x, y);
    valeurY = y
    valeurX = x;
    trouve(valeurX,valeurY,aleatoireMickeyX, aleatoireMickeyY);
    perdu(valeurX, valeurY,aleatoireMechant1X,aleatoireMechant1Y);
    perdu(valeurX,valeurY,aleatoireMechant2X, aleatoireMechant2Y);
    console.log(valeurX, valeurY);
}

function deplacerSarahBas() {
    ctx.clearRect(x, y, 50, 50);
    x = valeurX;
    if (y < 450) {
        y += 5;
    } else {
        y = 450;
    }
    ctx.drawImage(sarah(), x, y);
    valeurY = y
    valeurX = x;
    trouve(valeurX,valeurY,aleatoireMickeyX, aleatoireMickeyY);
    perdu(valeurX, valeurY,aleatoireMechant1X,aleatoireMechant1Y);
    perdu(valeurX,valeurY,aleatoireMechant2X, aleatoireMechant2Y);
    console.log(valeurX, valeurY);
    }


function changerDirection(e){
    switch (e.key){
        case "ArrowLeft": deplacerSarahGauche(); break;
        case "ArrowRight": deplacerSarahDroite(); break;
        case "ArrowDown": deplacerSarahBas(); break;
        case "ArrowUp": deplacerSarahHaut(); break;
    }
}

document.getElementById("btnDroite").addEventListener("click", deplacerSarahDroite);
document.getElementById("btnGauche").addEventListener("click", deplacerSarahGauche);
document.getElementById("btnHaut").addEventListener("click", deplacerSarahHaut);
document.getElementById("btnBas").addEventListener("click", deplacerSarahBas);

document.onkeydown = changerDirection;

window.onload = setInterval(deplacementMechant, 150);
window.onload = setInterval(deplacementMechant2, 150);

console.log(aleatoireMickeyX, aleatoireMickeyY);

