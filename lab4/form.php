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
        <!--<style>
/* Сообщения об ошибках и поля с ошибками выводим с красным бордюром. */
            .error {
                border: 2px solid red;
                background-color:#b3350379
            }
            /*.messages{
                color: #ff2d2d;
            }*/
            .result{
                color: white;
            }
            .error_gen {
                color:rgb(160, 32, 0);
            }
        </style>-->

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
                    <li> <a class="px-sm-2" href="project4/projects/1/famous.html"> Известные пираты </a></li>
                    <li> <a class="px-sm-2" href="project4/projects/1/myths.html"> Мифы о пиратах </a></li>
                    <li> <a class="px-sm-2" href="project4/projects/1/bio.html"> Пиратский кодекс </a></li>
                </ul>
            </nav>
            <div id = "top">
                <div class = "content container-fluid mt-sm-0">   
                    <div id="Форма"><h1>Форма</h1>
                        <form class="pl-sm-3" action="index.php"
                            method="POST"><?php
                            if (!empty($messages)) {
                              print('<div id="messages">');
                              // Выводим все сообщения.
                              foreach ($messages as $message) {
                                print($message);
                              }
                              print('</div>');
                            }
                            
                            // Далее выводим форму отмечая элементы с ошибками классом error
                            // и задавая начальные значения элементов ранее сохраненными.
                            ?>
                            <label class="l">
                                ФИО:<br>
                                <input name="name" <?php if ($errors['name']) {print 'class="error"';} ?> value="<?php print $values['name']; ?>"
                                value="ok">
                            </label><br>
                            <label class="l">
                                Номер телефона(начиная с +):<br>
                                <input name="number" <?php if ($errors['number']) {print 'class="error"';} ?> value="<?php print $values['number']; ?>"
                                value="ok"
                                type="tel">
                            </label><br>
                            <label class="l">
                                email:<br>
                                <input name="email" <?php if ($errors['email']) {print 'class="error"';} ?> value="<?php print $values['email']; ?>"
                                value="ok"
                                type="email">
                            </label><br>
                            <label class="l">
                                Дата рождения:<br>
                                <input name="bdate" <?php if ($errors['bdate']) {print 'class="error"';} ?> value="<?php print $values['bdate']; ?>"
                                value="ok"
                                type="date">
                            </label><br>
                             <div <?php if ($errors['gen']) {print 'class="error_gen"';} ?><?php if (!$errors['gen']) {print 'class="l"';} ?>>
                            <label>Пол:<br>
                            <input type="radio"  <?php if ($errors['gen']) {print 'class="error"';} ?>
      <?php if ($values['gen']=='female') {print 'checked="checked"';} ?>
                            name="gender" value="female">
                            Женский</label>
                            <label><input type="radio"  <?php if ($errors['gen']) {print 'class="error"';} ?>
      <?php if ($values['gen']=='male') {print 'checked="checked"';} ?>
                            name="gender" value="male">
                            Мужской</label><br>
                             </div>
                             <?php 
      $user_languages = explode(",",  $values['languages']);
      ?>
                            <label class="l">
                                Любимый язык программирования:
                                <br>
                                <select name="languages[]" multiple="multiple" <?php if ($errors['languages']) {print 'class="error"';} ?>>
                                <option value="Pascal" <?php if(in_array('Pascal', $user_languages)) {print 'selected="selected"';}?>>Pascal</option>
                                <option value="C" <?php if(in_array('C', $user_languages)) {print 'selected="selected"';}?>>C
                                <option value="C++" <?php if(in_array('C++', $user_languages)) {print 'selected="selected"';}?>>C++
                                <option value="JavaScript" <?php if(in_array('JavaScript', $user_languages)) {print 'selected="selected"';}?>>JavaScript
                                <option value="PHP" <?php if(in_array('PHP', $user_languages)) {print 'selected="selected"';}?>>PHP
                                <option value="Python" <?php if(in_array('Python', $user_languages)) {print 'selected="selected"';}?>>Python
                                <option value="Java" <?php if(in_array('Java', $user_languages)) {print 'selected="selected"';}?>>Java
                                <option value="Haskel" <?php if(in_array('Haskel', $user_languages)) {print 'selected="selected"';}?>>Haskel
                                <option value="Clojure" <?php if(in_array('Clojure', $user_languages)) {print 'selected="selected"';}?>>Clojure
                                <option value="Prolog" <?php if(in_array('Prolog', $user_languages)) {print 'selected="selected"';}?>>Prolog
                                <option value="Scala" <?php if(in_array('Scala', $user_languages)) {print 'selected="selected"';}?>>Scala
                                </select>
                            </label><br>
                            <label class="l">
                                Биография:<br>
                                <textarea name="biography" <?php if ($errors['biography']) {print 'class="error"';} ?>><?php print $values['biography']; ?></textarea>
                            </label><br>
                            <label class="l">   
                            С контрактом ознакомлен:<br>
                            <input type="checkbox" name="checkbox" <?php if ($errors['checkbox']) {print 'class="error"';} ?>  <?php if (!$errors['checkbox']) {print 'checked="checked"';} ?>> 
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
