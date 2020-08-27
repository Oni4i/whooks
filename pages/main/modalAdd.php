<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link href="./../../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="index.css" rel="stylesheet">

    <title>Gate Dev</title>
</head>

<body>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
        Добавить
    </button>

    <!-- Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавление кошелька</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">

                        <div class="form-group">
                            <label for="exampleInputPhone">Номер телефона</label>
                            <input type="phone" id="inputPhone" class="form-control" placeholder="+7 (999) 999 23 23"
                                required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputToken">Токен кошелька</label>
                            <input type="token" id="inputToken" class="form-control" placeholder="" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputToken">Срок действия токена</label>
                            <input type="token" id="inputToken" class="form-control" placeholder="дд.мм.гггг" required
                                autofocus>
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Аккаунт процессинга</label>
                            <select class="form-control" id="exampleFormControlSelect1">
                                <option>token 1</option>
                                <option>token 2</option>
                                <option>token 3</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Токенизированная карта</label>
                            <select class="form-control" id="exampleFormControlSelect1">
                                <option>token 1</option>
                                <option>token 2</option>
                                <option>token 3</option>
                            </select>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
</body>

</html>