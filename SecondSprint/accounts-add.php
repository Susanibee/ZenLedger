<?php session_start();
if(isset($_SESSION["admin"]))
{
?>
<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <link href="style/nonregisterstyle.css" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>ZenLedger - Accounts Add</title>
        <!-- Note(Fran): This CSS is **necessary** for html tabbing effect to work. All other CSS goes in a separate stylesheet! -->
        <style>
            [data-tab-info] {
                display: none;
            }

            .active[data-tab-info] {
                display: block;
            }
            .tab-header {
                cursor:pointer;
                margin: 4em;
            }
        </style>
    </head>
    <body>
        <main>
        <?php include('snippets/logged-in-top-bar.php'); ?>
        <h1> Accounts </h1>
        <hr>
        </main>

        <a href="accounts-add.php" style="color:white;background-color:#7fb285;" ><span class="tab-header">Add</span></a>
        <a href="accounts-deactivate.php"><span class="tab-header">Deactivate</span></a>
        <a href="accounts-edit.php"><span class="tab-header">Edit</span></a>
        <a href="accounts-view.php"><span class="tab-header">View</span></a>

        <div class="tab" id="Add-Tab">
        <br>
            <form method="POST" action="action-create-account.php"> 
                <label for="account-name">Account Name</label>
                <input type="text" name="account-name" id="account-name" required /><br>

                <label for="account-number">Account Number</label>
                <input type="number" min="0" step="1" name="account-number" id="account-number" oninput="this.value = Math.round(this.value);" required /><br>

                <label for="account-description">Account Description</label>
                <input type="text" name="account-description" id="account-description"/><br>

                <label for="account-category">Account Category</label>
                <select name="account-category" id="account-category" onchange="configureDropDownLists(this,document.getElementById('account-subcategory'))" required>
                    <option value="Assets">Assets</option>
                    <option value="Liabilities">Liabilities</option>
                    <option value="Owners Equity">Owner's Equity</option>
                </select><br>

                <label for="account-subcategory">Account Subcategory</label>
                <select name="account-subcategory" id="account-subcategory" required>
                    <option value="subcategory">Subcategory</option>
                </select><br>

                <!-- Javascript to make dropdowns work efficiently:-->
                <script>
                function createOption(ddl, text) {
                  var opt = document.createElement('option');
                  opt.value = text;
                  opt.text = text;
                  ddl.options.add(opt);

                }
                function configureDropDownLists(ddl1, ddl2) {
                  var assets = ["Cash", "Accounts Receivable", "Inventory", "Prepaid Rent", "Prepaid Insurance", "General Equipment", "Furniture", "Supplies", "Buildings", "Vehicles", "Land", "Investments"];
                  var liabilities = ["Accounts Payable", "Taxes Payable", "Salaries Payable", "Interest Payable", "Debts Payable", "Notes Payable", "Accrued Expenses", "Unearned Revenue"];
                  var owners_equity = ['Retained Earnings', 'Stock', 'Dividends'];

                  switch (ddl1.value) {
                    case 'Assets':
                      ddl2.options.length = 0;
                      for (i = 0; i < assets.length; i++) {
                        createOption(ddl2, assets[i], assets[i]);
                      }
                      break;
                    case 'Liabilities':
                      ddl2.options.length = 0;
                      for (i = 0; i < liabilities.length; i++) {
                        createOption(ddl2, liabilities[i], liabilities[i]);
                      }
                      break;
                    case 'Owners Equity':
                      ddl2.options.length = 0;
                      for (i = 0; i < owners_equity.length; i++) {
                        createOption(ddl2, owners_equity[i], owners_equity[i]);
                      }
                      break;
                  }
                }

              configureDropDownLists(document.getElementById("account-category"),document.getElementById("account-subcategory"));
                </script>

                <table>
                    <tr>
                        <td style="border:none">
                            <label for="debit">Debit</label>
                            $<input type="number" name="debit" id="debit" min="0" step='0.01' value='0.00' placeholder='0.00' /><br>

                            <label for="credit">Credit</label>
                            $<input type="number" name="credit" id="credit" min="0" step='0.01' value='0.00' placeholder='0.00'
                            /><br>
                        </td>

                        <td style="border:none">
                            <label for="initial-balance-display">Initial Balance</label>
                            $<input style="border:0;  -moz-appearance: textfield; outline:none!important;" type="text" name="initial-balance-display" id="initial-balance-display" readonly/><br>
                            <input type="number" name="initial-balance" id="initial-balance" readonly hidden/><br>
                        </td>
                    </tr>
                </table>

                    <script>
                        const debit = document.getElementById("debit");
                        const credit = document.getElementById("credit");
                        var last_working_credit = credit.value;
                        var last_working_debit = debit.value;
                        const initial_balance = document.getElementById("initial-balance");
                        const initial_balance_display = document.getElementById("initial-balance-display");
                        initial_balance_display.value = 0;

                        debit.addEventListener("change", (event) => {
                            initial_balance.value = (parseFloat(credit.value) + parseFloat(debit.value)).toFixed(2);
                            initial_balance_display.value = initial_balance.value;
                            initial_balance_display.value = initial_balance_display.value.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
                        });
                        credit.addEventListener("change", (event) => {
                            initial_balance.value = (parseFloat(credit.value) + parseFloat(debit.value)).toFixed(2);
                            initial_balance_display.value = initial_balance.value;
                            initial_balance_display.value = initial_balance_display.value.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
                        });
                        credit.addEventListener("input", (event) => {
                            if(isNaN(event.data) && event.data!="."){
                                event.preventDefault();
                                credit.value = last_working_credit;
                            }
                            else {
                                //Note(Art): not safe but what even is anymore man
                                initial_balance.value = (parseFloat(credit.value) + parseFloat(debit.value)).toFixed(2);
                                initial_balance_display.value = initial_balance.value;
                                initial_balance_display.value = initial_balance_display.value.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
                                last_working_credit = credit.value;
                            }
                        });
                        debit.addEventListener("input", (event) => {
                            if(isNaN(event.data) && event.data!="."){
                                console.log(event.data);
                                event.preventDefault();
                                debit.value = last_working_debit;
                            }
                            else {
                                //Note(Art): not safe but what even is anymore man
                                initial_balance.value = (parseFloat(credit.value) + parseFloat(debit.value)).toFixed(2);
                                initial_balance_display.value = initial_balance.value;
                                initial_balance_display.value = initial_balance_display.value.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
                                last_working_debit = debit.value;
                            }
                        });

                    </script>

                <label for="order">Order</label>
                <input type="number" value="1" min="1" step="1" name="order" id="order" oninput="this.value = Math.round(this.value);" required /><br>

                <label for="statement">Statement</label>
                <select name="statement" id="statement" required>
                    <option value="is">IS</option>
                    <option value="bs">BS</option>
                    <option value="re">RE</option>
                </select><br>

                <label for="comment">Comment</label>
                <input type="text" name="comment" id="comment"/><br>

                <input type="submit" value="Create Account">
            </form>
        </div>
        <footer>
    <div class="booties"><a href="help.php" class="help-button">Need help?</a>
    </div>
    <script src="snippets/calendar.js"></script> 
    </footer>
    </body>
</html>
<?php
}
else {
    echo("Error: Unauthorized Page");
}
?>
