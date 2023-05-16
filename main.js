// возвращает cookie если есть или undefined
function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}


let login_block = document.querySelector('#login_block');
let login_form = document.querySelector('#login_form');
let message_div = document.querySelector('#message');
login_form.addEventListener('submit', (e) => {
    e.preventDefault();
    message_div.classList.add('d-none');

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'login.php');
    xhr.onreadystatechange = function () {
        if ( xhr.readyState === xhr.DONE && xhr.status === 200 ){
            let data = JSON.parse(xhr.responseText);

            if ( data.error ) {
                message_div.classList.remove('d-none');
                message_div.innerHTML = data.error;
            }else{
                showUser(1);
            }
        }
    };
    xhr.send( new FormData(login_form) );
});

let user_block = document.querySelector('#user_block');
let hello = document.querySelector('#hello');
function showUser(logged = 0) {
    login_block.classList.add('d-none');
    user_block.classList.remove('d-none');

    let xhr = new XMLHttpRequest();
    xhr.open('GET', 'user.php');
    xhr.onreadystatechange = function () {
        if ( xhr.readyState === xhr.DONE && xhr.status === 200 ){
            let data = JSON.parse(xhr.responseText);

            if ( data.error ) {
                login_block.classList.remove('d-none');
                if (logged) {
                    message_div.classList.remove('d-none');
                    message_div.innerHTML = data.error;
                }
                user_block.classList.add('d-none');

            }else{
                if (logged) hello.classList.remove('d-none');
                window.setTimeout(() => {
                    hello.classList.add('d-none');
                }, 3000);
                document.querySelector('#fio').innerHTML = data.fio;
                document.querySelector('#ava').src = data.image + data.id + '.jpg';
                document.querySelector('#bd').innerHTML = data.berthday;

                /* multi account
                let login = getCookie('login');
                if (login){
                    document.querySelector('#fio').innerHTML = data[login].fio;
                    document.querySelector('#ava').src = data[login].image + data[login].id + '.jpg';
                    document.querySelector('#bd').innerHTML = data[login].berthday;
                }

                if ( data.length > 1 ){
                    select_account.classList.remove('d-none');
                    //select_account.
                    let sessions = getCookie('session');
                    for ( session in sessions ){
                        //select_account.
                    }
                }*/
            }
        }
    };
    xhr.send();
}

/* multi account
let account_button = document.querySelector('#account');
account_button.addEventListener('click', () => {
    login_block.classList.remove('d-none');
    user_block.classList.add('d-none');
});

let select_account = document.querySelector('#select_account');
select_account.addEventListener('change', () => {
    document.cookie = 'login=';
    document.cookie = 'token=';
});*/

window.onload = () => {
    showUser(0);
}