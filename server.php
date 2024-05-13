<?php
// sciaga wejsc do bazy danych
// playerChose = pierwszy gracz wybral strone
// cell(x) = pole w ktorym gracz zagral
session_start();
// print_r($_SESSION['ready'], $_SESSION['player']);
function chooseSide($object)
{
    $conn = mysqli_connect('pma.ct8.pl', "m43143_admin", "Admin123", 'm43143_xo');
    $res = $conn->query("SELECT wartosc FROM `play` WHERE `pole`='playerChose'");
    if (mysqli_num_rows($res) == 0) {
        $player = $object->iWant;
        $conn->query("INSERT INTO `play`(`pole`, `wartosc`) VALUES ('playerChose','$player')");
        $res2 = $conn->query("SELECT pole,wartosc FROM `play` WHERE `pole` LIKE 'turn'");
        if (mysqli_num_rows($res2) == 0) {
            $conn->query("INSERT INTO `play`(`pole`, `wartosc`) VALUES ('turn','x')");
        }
        $conn->close();
        $_SESSION['player'] = $player;
    } else {
        echo "side has been picked";
    }
};
function checkForPickedSide()
{
    if (!$_SESSION['player']) {
        $conn = mysqli_connect('pma.ct8.pl', "m43143_admin", "Admin123", 'm43143_xo');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        };
        $res = $conn->query("SELECT wartosc FROM `play` WHERE `pole`='playerChose'");
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_array($res);
            if ($row['wartosc'] == $_SESSION['player']) {
                if ($row['wartosc'] == "x") {
                    $_SESSION['player'] = 'x';
                } else {
                    $_SESSION['player'] = 'o';
                }
            } else {
                if ($row['wartosc'] == "x") {
                    $_SESSION['player'] = 'o';
                } else {
                    $_SESSION['player'] = 'x';
                }
            }
        };
        echo $_SESSION['player'];
        $conn->close();
    } else {
        echo $_SESSION['player'];
    }
};
function turn($object)
{
    $cell = $object->cell;
    $side = $_SESSION['player'];
    $conn = mysqli_connect('pma.ct8.pl', "m43143_admin", "Admin123", 'm43143_xo');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    };
    $res = $conn->query("SELECT wartosc FROM `play` WHERE `pole` LIKE 'cell%'");
    if (mysqli_num_rows($res) == 0) {
        // pierwszy ruch gry
        for ($i = 0; $i < 9; $i++) {
            $num = $i + 1; //zeby zaczynalo sie od 1 a konczylo na 9
            $conn->query("INSERT INTO `play`(`pole`, `wartosc`) VALUES ('cell$num','')");
        }
        $conn->query("UPDATE `play` SET `wartosc`='$side' WHERE pole='cell$cell';");
        $side = $conn->query("SELECT wartosc FROM `play` WHERE `pole` LIKE 'turn'");
        $player = mysqli_fetch_array($side);
        if ($player['wartosc'] == 'x') {
            $conn->query("UPDATE `play` SET `wartosc`='o' WHERE pole='turn';");
        } else {
            $conn->query("UPDATE `play` SET `wartosc`='x' WHERE pole='turn';");
        }
    } else {
        $conn->query("UPDATE `play` SET `wartosc`='$side' WHERE pole='cell$cell';");
        $side = $conn->query("SELECT wartosc FROM `play` WHERE `pole` LIKE 'turn'");
        $player = mysqli_fetch_array($side);
        if ($player['wartosc'] == 'x') {
            $conn->query("UPDATE `play` SET `wartosc`='o' WHERE pole='turn';");
        } else {
            $conn->query("UPDATE `play` SET `wartosc`='x' WHERE pole='turn';");
        }
    }
    $conn->close();
    echo $_SESSION['player'];
}
function clearSQL()
{
    session_destroy();
    $conn = mysqli_connect('pma.ct8.pl', "m43143_admin", "Admin123", 'm43143_xo');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    };
    $conn->query("DELETE FROM `play` WHERE 1");
    $conn->close();
}
function checkForWin()
{
    $conn = mysqli_connect('pma.ct8.pl', "m43143_admin", "Admin123", 'm43143_xo');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    };
    $res = $conn->query("SELECT wartosc FROM `play` WHERE `pole` LIKE 'cell%'");
    if (mysqli_num_rows($res) == 0) {
        echo "no";
    } else {
        $arr = [];
        for ($l = 0; $l < mysqli_num_rows($res); $l++) {
            $row = mysqli_fetch_array($res);
            array_push($arr, $row['wartosc']);
        };
        if ($arr[0] == $arr[1] && $arr[2] == $arr[0] && $arr[0] != '') {
            echo "win for" . $arr[1];
        } else if ($arr[3] == $arr[4] && $arr[5] == $arr[3] && $arr[3] != '') {
            echo "win for" . $arr[3];
        } else if ($arr[6] == $arr[7] && $arr[8] == $arr[7] && $arr[8] != '') {
            echo "win for" . $arr[6];
        } else if ($arr[0] == $arr[3] && $arr[6] == $arr[3] && $arr[0] != '') {
            echo "win for" . $arr[0];
        } else if ($arr[1] == $arr[4] && $arr[7] == $arr[4] && $arr[4] != '') {
            echo "win for" . $arr[1];
        } else if ($arr[2] == $arr[5] && $arr[8] == $arr[5] && $arr[2] != '') {
            echo "win for" . $arr[5];
        } else if ($arr[0] == $arr[4] && $arr[8] == $arr[4] && $arr[0] != '') {
            echo "win for" . $arr[4];
        } else if ($arr[2] == $arr[4] && $arr[6] == $arr[4] && $arr[2] != '') {
            echo "win for" . $arr[4];
        } else {
            $tie = false;
            for ($p = 0; $p < count($arr); $p++) {
                if ($arr[$p] != '') {
                    $tie = true;
                } else {
                    $tie = false;
                    break;
                }
            }
            if ($tie) {
                echo 'win fortie';
            } else {
                echo 'no';
            }
        }
    }
}
function whoseGo()
{
    $conn = mysqli_connect('pma.ct8.pl', "m43143_admin", "Admin123", 'm43143_xo');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    };
    $res = $conn->query("SELECT pole,wartosc FROM `play` WHERE `pole` LIKE 'turn'");
    $answer = '';
    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_array($res);
        $answer = $row['wartosc'];
    };
    echo $answer;
    $conn->close();
}
function updateTable()
{
    $conn = mysqli_connect('pma.ct8.pl', "m43143_admin", "Admin123", 'm43143_xo');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    };
    $res = $conn->query("SELECT pole,wartosc FROM `play` WHERE `pole` LIKE 'cell%'");
    $answer = '';
    if (mysqli_num_rows($res) > 0) {
        for ($l = 0; $l < mysqli_num_rows($res); $l++) {
            $row = mysqli_fetch_array($res);
            $answer = $answer . $row['pole'] . "," . $row['wartosc'] . ";";
        };
    };
    echo $answer;
    $conn->close();
}
if (!empty($json = file_get_contents("php://input"))) {
    $object = json_decode($json);
    $action = $object->request;
    if ($action == 'side') {
        chooseSide($object);
    } elseif ($action == 'checkForPickedSide') {
        checkForPickedSide();
    } elseif ($action == 'clearSQL') {
        clearSQL();
    } elseif ($action == 'turn') {
        turn($object);
    } elseif ($action == 'updateTable') {
        updateTable();
    } elseif ($action == 'whoseGo') {
        whoseGo();
    } elseif ($action == 'checkForWin') {
        checkForWin();
    };
};
