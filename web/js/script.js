
// Définition de toute les variable nécéssaire au script :

 var h2 = document.getElementsByClassName('article_title')
 var divPage = document.getElementById('divPage')
 var div = document.getElementById('section')
 var array = new Array();
 var arrays = new Array();

init();

 /*
 Initialisation du script : récupére tout les éléments afficher par le twig et symfony. Les stocks dans un tableau d'élément HTML, à partir de 10 éléments stockés, le tableau est stocké dans un autre tableau
 Les tableau permettrons la gestions des page, quantité et affichage.
 Ensuite appel de la fonction ChevronFactory et PageFactory
  */

 function init()
 {

  var x = 0;

  for(var i = 0; i <= h2.length - 1; i++)
  {
   array[x] = h2[i]
   if (x == 10 || i == h2.length - 1)
   {
    arrays.push(array);
    array = [];
    x = -1;
   }
   x++
  }

  divPage.style.display = 'none'
  ChevronFactory( x )
  PageFactory( x )

 }

 /*
 Fonction PageFactory permet, grace à un entier ( x ) et au tableau initialiser au chargement de la page, d'afficher les éléments du tableau avec une boucle for.
 La fonction gére aussi l'affichage des chevron : Si l'index est 0 le chevron précédent n'existe pas à l'inverse si l'index correspond au dernier index du tableau, le chevron suivant n'existe pas
 L'appel de l'animation est défini en début de fonction puis à la création de la nouvelle div.
  */

 function PageFactory( x )
 {
  divPage.setAttribute('class', 'zoomOut')
  //Condition d'affichage des chevrons
  if(document.getElementsByClassName('page')[0].textContent <= 0)
  {
   document.getElementsByClassName('previous')[0].style.display = 'none'
  } else {
   document.getElementsByClassName('previous')[0].style.display = 'inline-block'
  }

  if(document.getElementsByClassName('page')[0].textContent >= arrays.length - 1)
  {
   document.getElementsByClassName('next')[0].style.display = 'none'
  } else {
   document.getElementsByClassName('next')[0].style.display = 'inline-block'
  }

  setTimeout(function(){
   div.removeChild(divPage)
   divPage = document.createElement('div')
   div.appendChild(divPage)
   divPage.setAttribute('class', 'zoomIn')
   console.log('divPage Reactivate : ', divPage)
   for(var i = 0; i <= arrays[x].length -1; i++)
   {
    console.log(arrays[x][i])
    divPage.appendChild(arrays[x][i])
   }
  }, 800)

 }


 //Gere l'appel de la fonction en fonction de x dans les liens pour les pages suivantes.

 function NextPage( x )
 {

  var p = document.getElementsByTagName('p')[0]
  p.textContent = x
  var aNext = document.getElementsByClassName('next')[0]
  var aPrevious = document.getElementsByClassName('previous')[0]
  aPrevious.setAttribute('onclick', 'PreviousPage('+(x - 1)+')')
  aNext.setAttribute('onclick', 'NextPage('+( x + 1)+')')
  var anim = 'zoomOut'
  PageFactory( ( x ), anim )
 }

 //Gere l'appel de la fonction en fonction de x dans les liens pour les pages précédentes.

 function PreviousPage( x )
 {
  var p = document.getElementsByTagName('p')[0]
  p.textContent = x
  var aPrevious = document.getElementsByClassName('previous')[0]
  var aNext = document.getElementsByClassName('next')[0]
  aPrevious.setAttribute('onclick', 'PreviousPage('+( x - 1)+')')
  aNext.setAttribute('onclick', 'NextPage('+( x + 1 )+')')
  var anim = 'zoomOut'
  PageFactory( ( x ), anim )
 }


 //Fonction appellée une seule fois qui créer les chevrons et l'index de la page.

 function ChevronFactory( x )
 {
  var aPrevious = document.createElement('a');
  document.getElementById('page').appendChild(aPrevious);
  aPrevious.setAttribute('onClick', 'PreviousPage('+(x - 1)+')')
  aPrevious.setAttribute('href', '#/'+page)
  aPrevious.setAttribute('class', 'previous')
  aPrevious.style.display = 'none'
  var span = document.createElement('span')
  span.setAttribute('class', 'glyphicon glyphicon-chevron-left arrow')
  aPrevious.appendChild(span)


  var p = document.createElement('p')
  document.getElementById('page').appendChild(p)
  p.setAttribute('class', 'page')
  p.style.display = 'inline-block'
  p.textContent = x

  var aNext = document.createElement('a');
  document.getElementById('page').appendChild(aNext);
  aNext.setAttribute('onClick', 'NextPage('+(x + 1)+')')
  aNext.setAttribute('href', '#/'+page)
  aNext.setAttribute('class', 'next')
  var span = document.createElement('span')
  span.setAttribute('class', 'glyphicon glyphicon-chevron-right')
  aNext.appendChild(span)
 }

 function validation()
 {
  confirm('Attention la suppression de votre profil est irreversible, etes vous sur de vouloir le supprimer ?')
 }







