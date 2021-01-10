<?php
    namespace Utils;

    class Invoices {
        private $_data;
        private $_reports;

        public function __construct($data, $reports){
            $this->_data = $data;
            $this->_reports = $reports;
        }

        public function createInvoice(){
            $total = 0;
            $body = '';
            foreach($this->_reports as $report){
                $total += $report[2];
                $body .= "
                <tr>
                    <td class='service'>".htmlspecialchars($report[1],ENT_QUOTES)."</td>
                    <td class='desc'>".htmlspecialchars($report[0],ENT_QUOTES)."</td>
                    <td class='unit'>".htmlspecialchars($report[2],ENT_QUOTES)."€</td>
                    <td class='qty'>1</td>
                    <td class='total'>".htmlspecialchars($report[2],ENT_QUOTES)."€</td>
                </tr>";
            }
            $invoices = "
            <style>
                .clearfix:after {
                    content: '';
                    display: table;
                    clear: both;
                }

                a {
                    color: #5D6975;
                    text-decoration: underline;
                }

                body {
                    position: relative;
                    width: 21cm;  
                    height: 29.7cm; 
                    margin: 0 auto; 
                    color: #001028;
                    background: #FFFFFF; 
                    font-family: Arial, sans-serif; 
                    font-size: 12px; 
                    font-family: Arial;
                    padding: 10px;
                }

                header {
                    padding: 10px 0;
                    margin-bottom: 30px;
                }

                #logo {
                    text-align: center;
                    margin-bottom: 10px;
                }

                #logo img {
                    width: 90px;
                }

                h1 {
                    border-top: 1px solid  #5D6975;
                    border-bottom: 1px solid  #5D6975;
                    color: #5D6975;
                    font-size: 2.4em;
                    line-height: 1.4em;
                    font-weight: normal;
                    text-align: center;
                    margin: 0 0 20px 0;
                }

                #project {
                    float: left;
                }

                #project span {
                    color: #5D6975;
                    text-align: right;
                    width: 52px;
                    margin-right: 10px;
                    display: inline-block;
                    font-size: 0.8em;
                }

                #company {
                    float: right;
                    text-align: right;
                }

                #project div,
                #company div {
                    white-space: nowrap;        
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    border-spacing: 0;
                    margin-bottom: 20px;
                }

                table tr:nth-child(2n-1) td {
                    background: #F5F5F5;
                }

                table th,
                table td {
                    text-align: center;
                }

                table th {
                    padding: 5px 20px;
                    color: #5D6975;
                    border-bottom: 1px solid #C1CED9;
                    white-space: nowrap;        
                    font-weight: normal;
                }

                table .service,
                table .desc {
                    text-align: left;
                }

                table td {
                    padding: 20px;
                    text-align: right;
                }

                table td.service,
                table td.desc {
                    vertical-align: top;
                }

                table td.unit,
                table td.qty,
                table td.total {
                    font-size: 1.2em;
                }

                table td.grand {
                    border-top: 1px solid #5D6975;;
                }

                #notices .notice {
                    color: #5D6975;
                    font-size: 1.2em;
                }

                footer {
                    color: #5D6975;
                    width: 100%;
                    height: 30px;
                    position: absolute;
                    bottom: 0;
                    border-top: 1px solid #C1CED9;
                    padding: 8px 0;
                    text-align: center;
                }
            </style>
            <body>
                <header class='clearfix'>
                    <div id='logo'>
                        <h2>".ucwords($this->_data['prenom'])." ".ucwords($this->_data['nom'])."</h2>
                    </div>
                    <h1>INVOICE 2020".date_parse($this->_data['month'])['month'].$this->_data['number']."</h1>
                    <div id='company' class='clearfix'>
                        <div>".ucfirst(strtolower($this->_data['prenom']))." ".ucwords($this->_data['nom'])."</div>
                        <div>".$this->_data['address']."</div>
                        <div>".$this->_data['phone']."</div>
                        <div><a href='".$this->_data['email']."'>".$this->_data['email']."</a></div>
                        <div><span>SIRET :</span> ".$this->_data['SIRET']."</div>
                        <div><span>VAT :</span> ".$this->_data['VAT']."</div>
                    </div>
                    <div id='project'>
                        <div><span>PROJECT</span>INTIGRITI - BUGBOUNTY</div>
                        <div><span>CLIENT</span>INTIGRITI NV</div>
                        <div><span>BTW</span>BE 0660.623.646</div>
                        <div><span>ADDRESS</span>Provinciale Steenweg 39, 2627 Schelle, BE</div>
                        <div><span>EMAIL</span> <a href='mailto:support@intigriti.com'>support@intigriti.com</a></div>
                        <div><span>DATE</span> ".$this->_data['month'].", 2020</div>
                    </div>
                </header>
                <main>
                    <table>
                        <thead>
                        <tr>
                            <th class='service'>DATE</th>
                            <th class='desc'>TITLE</th>
                            <th>PRICE EXCL.</th>
                            <th>QTY</th>
                            <th>TOTAL EXCL.</th>
                        </tr>
                        </thead>
                        <tbody>
                            ".$body."
                            <tr>
                                <td colspan='4'>SUBTOTAL EXCL.</td>
                                <td class='total'>".$total."€</td>
                            </tr>
                            <tr>
                                <td colspan='4'>VAT 0%</td>
                                <td class='total'>".$total."€</td>
                            </tr>
                            <tr>
                                <td colspan='4' class='grand total'>TOTAL</td>
                                <td class='grand total'>".$total."€<br /></td>
                            </tr>
                        </tbody>
                    </table>
                    <div id='notices'>
                        <div>NOTICE:</div>
                        <div class='notice'>VAT not applicable, art. 293 B of CGI</div>
                    </div>
                    <br />
                    <br />
                    <br />

                    <table>
                        <thead>
                            <tr>
                                <th class='service'></th>
                                <th class='desc'>BANK</th>
                                <th>IBAN</th>
                                <th>BIC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class='service'></td>
                                <td class='desc'>".$this->_data['BANK']."</td>
                                <td class='unit'>".$this->_data['IBAN']."</td>
                                <td class='qty'>".$this->_data['BIC']."</td>
                            </tr>
                        </tbody>
                    </table>
                </main>
            </body>
            ";
            return $invoices;
        }
    }
?>