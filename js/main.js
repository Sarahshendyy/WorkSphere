


// card 
const setPriority = document.getElementById('setPriority')
const priorityShow = document.getElementById('priorityShow')
const options = document.getElementsByClassName('priorityOpt')


setPriority.addEventListener('click', function () {
    priorityShow.classList.add('d-show')
    // console.log('ayhaga');
})

for(var i = 0; i < options.length; i++){
    options[i].addEventListener('click', function(){
        priorityShow.classList.remove('d-show')
        console.log('omar');
    })
}