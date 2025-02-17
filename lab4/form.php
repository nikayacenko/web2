<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>зомбиробоповарозавр</title>
        <link href = "project4/projects/1/static/styles/style.css" rel = "stylesheet">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <script
          src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    </head>
    <body>
        <header>
            <div id = "fir">
                <img id="logo" src="project4/projects/1/static/image/флаг.png" alt="Логотип">
                <h1 class="name">Мистер Гиббс: все о пиратах</h1>
            </div>
            </header>
        <div class = "page px-sm-0 mr-sm-0 mb-sm-2">
            <nav class="mt-sm-0 mb-sm-0 px-sm-0 mx-sm-0">
                <ul class="px-3">
                    <li> <a class="px-sm-2" href="famous.html"> Известные пираты </a></li>
                    <li> <a class="px-sm-2" href="myths.html"> Мифы о пиратах </a></li>
                    <li> <a class="px-sm-2" href="bio.html"> Пиратский кодекс </a></li>
                </ul>
            </nav>
            <div id = "top">
                <div class = "content container-fluid mt-sm-0">   
                    <div id="Форма"><h1>Форма</h1>
                        <form class="pl-sm-3" action="index.php"
                            method="POST">
                            <label>
                                ФИО:<br>
                                <input name="name"
                                value="">
                            </label><br>
                            <label>
                                Номер телефона:<br>
                                <input name="number"
                                value=""
                                type="tel">
                            </label><br>
                            <label>
                                email:<br>
                                <input name="email"
                                value=""
                                type="email">
                            </label><br>
                            <label>
                                Дата рождения:<br>
                                <input name="bdate"
                                value=""
                                type="date">
                            </label><br>
                            <label>Пол:<br>
                            <input type="radio" checked="checked"
                            name="gender" value="ж">
                            Женский</label>
                            <label><input type="radio"
                            name="gen" value="м">
                            Мужской</label><br>
                            <label>
                                Любимый язык программирования:
                                <br>
                                <select name="languages"
                                multiple="multiple">
                                <option value="Значение1">Pascal</option>
                                <option value="Значение2" >C
                                <option value="Значение3" >C++
                                <option value="Значение3" >JavaScript
                                <option value="Значение3" >PHP
                                <option value="Значение3" >Python
                                <option value="Значение3" >Java
                                <option value="Значение3" >Haskel
                                <option value="Значение3" >Clojure
                                <option value="Значение3" >Prolog
                                <option value="Значение3" >Scala
                                </select>
                            </label><br>
                            <label>
                                Биография:<br>
                                <textarea name="biography"></textarea>
                            </label><br>
                            <label>   
                            С контрактом ознакомлен:<br>
                            <input type="checkbox" name="checkbox">
                            </label><br>
                            <input type = "submit" value = "Сохранить">
                        </form>
                    </div> 
                    <p><a href = "#top">наверх</a></p>
                </div>
            </div>
        </div>
    <footer>
       <p> (с) Яценко Вероника</p>
    </footer> 
    </body>
</html>
