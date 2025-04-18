<?php
session_start();
$number = $_GET['number'];
if(empty($number)) {
    echo "Unauthorized Page";
}
else {


if (empty($_SESSION['selected_customer'])) {
    header("Location: index.php"); 
    exit();
}
include("snippets/cosmic-message.php");
?>

<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <link href="style/nonregisterstyle.css" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>ZenLedger - Journal Entry Edit</title>
        <style>
            .debit-subentry, .credit-subentry {
                margin-top: 1em;
            }
            #date_of_transaction {
                font-size: 1.2rem;
            }
            .remove {
                margin-left: 0.5em;
            }
            
            input[type=number] {
                max-width: 8em;
                -moz-appearance: textfield;
            }
            button:disabled {
                background-color: #667356;
           }
        </style>
    </head>
    <body>
        <script>
            function AddNewSubentry(is_crediting, is_first)
            {
                const button = is_crediting ? document.getElementById("add-credit") : document.getElementById("add-debit"); 
                const new_section = document.createElement("div");
                const class_name = is_crediting ? "credit-subentry" : "debit-subentry";
                new_section.classList.add(class_name);
                const new_select = document.createElement("select");
                const accounts_names_id = 
                <?php
                    $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
                    or die('Could not connect: ' . pg_last_error());

                    // SQL query to read columns
                    $html_safe_customer = htmlspecialchars($_SESSION['selected_customer']);
                    $query = "select account_name, account_id FROM chart_of_accounts where customer_name='$html_safe_customer' AND is_activated='true';";
                    $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
                    $array = pg_fetch_all($result, PGSQL_NUM);
                    echo json_encode($array);
                ?>;
                accounts_names_id.forEach((account) => {
                    const option = document.createElement("option");
                    option.innerHTML = account[0];
                    option.value = account[1];
                    new_select.appendChild(option);
                });
                new_section.appendChild(new_select);

                const money_label = document.createElement("label");
                money_label.innerHTML = " $";
                const money_input = document.createElement("input");
                money_input.type = "number";
                money_input.value = 0;
                var last_working_money_input = money_input.value;
                money_input.setAttribute("min","0");
                money_input.setAttribute("step","0.01");

                money_input.addEventListener("input", (event) => {
                    if(isNaN(event.data) && event.data!=".") {
                        console.log(event.data);
                        event.preventDefault();
                        money_input.value = last_working_money_input;
                    }
                    else if(Number.isNaN(Number.parseFloat(money_input.value))) {
                        console.log(event.data);
                        event.preventDefault();
                        money_input.value = last_working_money_input;
                    }
                    else {
                        last_working_money_input = money_input.value;
                    }
                });
                money_input.addEventListener("focusout", (event) => {
                    money_input.value = parseFloat(money_input.value).toFixed(2);
                });
                new_section.appendChild(money_label);
                new_section.appendChild(money_input);

                const remove = document.createElement("button");
                remove.innerHTML = "-";
                remove.classList.add("remove");
                if(is_first) { remove.setAttribute("disabled", "d"); }
                remove.addEventListener("click", (event) =>
                {
                    new_section.remove();
                });
                new_section.appendChild(remove);

                button.before(new_section);
            }
        </script>
        <main>
            <?php include('snippets/logged-in-top-bar.php'); ?>
            <h1> Journal Entry Edit</h1>
            <hr>
            <p id="error" style="color: red;">
            </p>
            <label>Date of Transaction</label>
            <input id="date_of_transaction" type="date"> <!-- JS under sets max to today -->
            <script>
                const date = document.getElementById("date_of_transaction")
                const today = new Date();
                <?php
                    $dbconn = pg_connect("postgresql://zenteamrole:npg_I7ZNn1hVqjtA@ep-raspy-smoke-a5pyv0mk-pooler.us-east-2.aws.neon.tech/zenledgerdb?sslmode=require")
                    or die('Could not connect: ' . pg_last_error());
                    $query = "select * from journal_entries where post_reference='$number'";
                    $result = pg_query($dbconn, $query) or die('Query failed: ' . pg_last_error());
                    $arr = pg_fetch_row($result, null, PGSQL_ASSOC);
                ?>
                const original = new Date("<?php echo $arr['date']?>");
                console.log(original);
                date.valueAsDate = original; date.max = today;

            </script>
            <label>Description</label>
            <input id="description" type="text" value="<?php echo $arr['description']?>">
            <label>Comments</label>
            <input id="comments" type="text" value="<?php echo $arr['comments']?>">
            <label>Attachment</label>
            <input id="attachment" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.jpg,.png">

            <div style="display: grid; grid-template-columns: 1fr 1fr; text-align: center;">
                <div id="debit" style="border-right: 1px solid darkgrey;">
                    <h2 style="border-bottom: 1px solid darkgrey;">Debit (Dr)</h2>
                    <button id="add-debit" type="button" onclick="AddNewSubentry(false, false);">+</button>
                </div>

                <div id="credit">
                    <h2 style="border-bottom: 1px solid darkgrey;">Credit (Cr)</h2>
                    <button id="add-credit" type="button" onclick="AddNewSubentry(true, false);">+</button>
                </div>
            </div>
            <div style="margin-top: 1.5em; display:flex; align-items:center; justify-content: center;">
                <button id="request"> 
                    <?php if(isset($_SESSION['manager'])) { ?>Update<?php } else {?> Request to Enter <?php } ?>
                </button>
                <script>
                    const error = document.getElementById("error");
                    const button = document.getElementById("request");
                    const description = document.getElementById("description");
                    const comments = document.getElementById("comments");
                    const date_of_transaction = document.getElementById("date_of_transaction");
                    
                    button.addEventListener("click", (event) => {
                        const debit_subentries = document.getElementsByClassName("debit-subentry");
                        const credit_subentries = document.getElementsByClassName("credit-subentry");
                        var debit_number_list = [];
                        var credit_number_list = [];
                        var debit_value_list= [];
                        var credit_value_list= [];

                        var total_debit = 0.0;
                        var total_credit = 0.0;

                        var is_empty_account = false;
                        for(let entry of debit_subentries) {
                            var value = Number.parseFloat(entry.children[2].value)
                            if(value == 0.0) {
                                is_empty_account = true;
                                break;
                            }
                            total_debit += value;
                            debit_number_list.push(entry.children[0].value);
                            debit_value_list.push(value);
                        }
                        if(!is_empty_account)
                        {
                            for(let entry of credit_subentries) {
                                var value = Number.parseFloat(entry.children[2].value)
                                if(value == 0.0) {
                                    is_empty_account = true;
                                    break;
                                }
                                total_credit += value;
                                credit_number_list.push(entry.children[0].value);
                                credit_value_list.push(value);
                            }
                            if(!is_empty_account)
                            {
                                if(total_debit == total_credit) {
                                    const hasDuplicates = (arr) => arr.length !== new Set(arr).size;
                                    if(!hasDuplicates(debit_number_list)) {
                                        if(!hasDuplicates(credit_number_list))
                                        {
                                            // TODO(Art): HERE FINISH
                                            const intersection = debit_number_list.filter(credit_number => credit_number_list.includes(credit_number));
                                            if(intersection.length == 0)
                                            {
                                                error.innerHTML = "";
                                                let data = { 
                                                    date_of_transaction:   date_of_transaction.value,
                                                    description:           description.value,
                                                    comments:              comments.value,
                                                    debit_accounts:        debit_number_list,
                                                    debit_values:          debit_value_list,
                                                    credit_accounts:       credit_number_list,
                                                    credit_values:         credit_value_list
                                                };

                                                /* TODO: Update the sending!
                                                async function send() {
                                                    try {
                                                       const response = await fetch("action-add-journal-entry.php", {
                                                                            method: "POST",
                                                                            redirect: 'follow',
                                                                            headers: {'Content-Type': 'application/json'}, 
                                                                            body: JSON.stringify(data) });
                                                       if (!response.ok) {
                                                                 throw new Error(`Response status: ${response.status}`);
                                                        }
                                                       const json_response = await response.json();
                                                       console.log(json_response);
                                                       <?php if(isset($_SESSION['manager'])) { ?>
                                                           window.location.replace("journal-view.php#"+json_response);
                                                       <?php } else { ?>
                                                           window.location.replace("journal-entry.php");
                                                       <?php } ?>
                                                    }
                                                    catch(error) { console.error(error); }
                                                }
                                                send();
                                                */
                                            }
                                            else
                                            {
                                                error.innerHTML = "ERROR: Cr and Dr have subentries that share an account.\n";
                                            }
                                        }
                                        else {
                                            error.innerHTML = "ERROR: Cr side has subentries with duplicate accounts.";
                                        }
                                    }
                                    else {
                                        error.innerHTML = "ERROR: Dr side has subentries with duplicate accounts.";
                                    }
                                }
                                else {
                                    console.log("t_deb: "+total_debit+"\n");
                                    console.log("t_cre: "+total_credit+"\n");
                                    error.innerHTML = "ERROR: The total Dr and total Cr do not match.";
                                }
                            }
                            else {
                                error.innerHTML = "ERROR: Cr side has at least one subentry with a zero dollar amount.";
                            }
                        }
                        else {
                            error.innerHTML = "ERROR: Dr side has at least one subentry with a zero dollar amount.";
                        }
                    });
                </script>
            </div>
        </main>
        <footer>
        <div class="booties"><a href="help.php" class="help-button">Need help?</a>
        </div>
        <script>
            <?php
                $query="select (unnest).is_crediting, (unnest).amount, (unnest).account_id from (select unnest(subentries) from journal_entries where post_reference='$number')";
                $result = pg_query($dbconn, $query);
                $first_d_sub = true;
                $first_c_sub = true;
                $d_subs = 0;
                $c_subs = 0;
                while ($row = pg_fetch_assoc($result)) {
                    if($row['is_crediting']=='f') {
                        echo "AddNewSubentry(false, $first_d_sub);";
                        $d_subs = $d_subs + 1;
                        if($first_d_sub){$first_d_sub=false;}

                        // retrieve and change value
                        ?>
                        var subentry = document.getElementsByClassName("debit-subentry")[<?php echo $d_subs ?>-1];
                        var input = subentry.getElementsByTagName("input")[0];
                        input.value = <?php echo $row['amount'] ?>;

                        var options = subentry.getElementsByTagName("option");
                        for(let i=0; i < options.length; i++) {
                            var option = options[i];
                            if(option.value=="<?php echo $row['account_id'] ?>") {
                                option.selected = true;
                            }
                        }
                        <?php 
                    }
                    else {
                        echo "AddNewSubentry(true, $first_c_sub);";
                        $c_subs = $c_subs + 1;
                        if($first_c_sub){$first_c_sub=false;}

                        // retrieve and change value
                        ?>
                        var subentry = document.getElementsByClassName("credit-subentry")[<?php echo $c_subs ?>-1];
                        var input = subentry.getElementsByTagName("input")[0];
                        input.value = <?php echo $row['amount'] ?>;

                        var options = subentry.getElementsByTagName("option");
                        for(let i=0; i < options.length; i++) {
                            var option = options[i];
                            if(option.value=="<?php echo $row['account_id'] ?>") {
                                option.selected = true;
                            }
                        }
                        <?php 
                    }
                }
            ?>
        </script>
        </footer>
        </main>
    </body>
</html>
<?php } ?>
