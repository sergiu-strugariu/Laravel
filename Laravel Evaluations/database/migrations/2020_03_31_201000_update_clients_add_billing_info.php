<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateClientsAddBillingInfo extends Migration
{

    private $clients = array(
            array('id' => '1','billing_company_name' => 'WIPRO TECHNOLOGIES SRL','billing_registry' => 'J40/13300/2006','billing_cif' => 'RO 18944060','billing_address' => 'Calea Floreasca, nr. 169A, Cladirea B, Etaj 1, 2 si 3, Bucuresti, Sector 1','billing_iban' => 'RO72CITI0000000724634001','billing_bank' => 'CITIBANK EUROPE plc .DUBLIN','billing_capital' => '-','billing_contract_no' => '03','billing_contract_date' => '22/03/2018'),
            array('id' => '2','billing_company_name' => 'S.C. I FUTURE NXT S.R.L.','billing_registry' => 'J40/12347/2011','billing_cif' => 'RO 29233349','billing_address' => 'Splaiul Independentei 319-C, Corp A, Mezzanine, Nr. 2, sector 6, Bucuresti','billing_iban' => 'RO95RNCB0081124474930001','billing_bank' => 'BCR','billing_capital' => '-','billing_contract_no' => '30','billing_contract_date' => '01/11/2018'),
            array('id' => '3','billing_company_name' => 'SOCIETE GENERALE EUROPEAN BUSINESS SERVICES SA','billing_registry' => 'J40/151/2011','billing_cif' => 'RO 27883477','billing_address' => 'Str. Preciziei, nr 24, cladire H4, et. 5 și parter, Sector 6, Bucuresti','billing_iban' => 'RO88BRDE450SV54117514500','billing_bank' => 'BRD Groupe Société Générale','billing_capital' => '-','billing_contract_no' => '635BIS','billing_contract_date' => '18/04/2018'),
            array('id' => '11','billing_company_name' => 'BRITISH AMERICAN SHARED SERVICES (EUROPE) SRL','billing_registry' => 'J40/7328/2008','billing_cif' => 'RO 23774019','billing_address' => 'Splaiul Independentei, nr. 319, Cladirea Sema Parc "City Building", etaj I, sector 6, Bucuresti','billing_iban' => 'RO92CITI0000000724495028','billing_bank' => 'Citibank Europe plc, Sucursala Romaniei','billing_capital' => '-','billing_contract_no' => '42','billing_contract_date' => '03/06/2019'),
            array('id' => '12','billing_company_name' => 'MSE-MAJOREL STRATEGY & EXPERTISE SRL','billing_registry' => 'J08/753/2010','billing_cif' => 'RO 27152965','billing_address' => 'Str. Ionescu Crum, nr. 1, Brasov Business Park, Turn 1, etaj 6, Brasov','billing_iban' => 'RO22CITI0000000709506005','billing_bank' => 'Citibank Europe plc, Dublin -Sucursala Romania','billing_capital' => '-','billing_contract_no' => '459','billing_contract_date' => '13/09/2017'),
            array('id' => '13','billing_company_name' => 'SCHNEIDER ELECTRIC ROMANIA SRL','billing_registry' => 'J40/1108/1997','billing_cif' => 'RO 9189605','billing_address' => 'Str. Gara Herastrau, nr 4, Cladirea Green Court Bucharest, Cladire A, etaj 1 si etaj 2, sector 2, Bucuresti','billing_iban' => 'RO91BRDE450SV01040024500','billing_bank' => 'BRD GSC SMCC','billing_capital' => '-','billing_contract_no' => '31','billing_contract_date' => '05/11/2018'),
            array('id' => '14','billing_company_name' => 'OMV Petrom SA','billing_registry' => 'J40/8302/1997','billing_cif' => 'RO 1590082','billing_address' => 'Strada Coralilor, nr 22, sector 1, Bucuresti, ( "Petrom City" ), Sector 1, Bucuresti','billing_iban' => '-','billing_bank' => '-','billing_capital' => '-','billing_contract_no' => '99003969','billing_contract_date' => '27/03/2020'),
            array('id' => '15','billing_company_name' => 'SC OFFICE DEPOT SERVICE CENTER SRL','billing_registry' => 'J12/1801/2008','billing_cif' => 'RO 22224777','billing_address' => 'Al. Vaida Voievod, nr. 53-55, Cluj Napoca, jud. Cluj','billing_iban' => 'RO33BACX0000000458846001','billing_bank' => 'Unicredit Tiriac Bank','billing_capital' => '-','billing_contract_no' => '21','billing_contract_date' => '01/08/2018'),
            array('id' => '16','billing_company_name' => 'WEBHELP ROMANIA SRL','billing_registry' => 'J40/14080/2006','billing_cif' => 'RO 18988172','billing_address' => 'Str. Doctor Iacob Felix, nr. 65-69, Sector 1, Bucuresti','billing_iban' => 'RO38BRDE445SV65833294450','billing_bank' => 'BRD','billing_capital' => '-','billing_contract_no' => '20','billing_contract_date' => '01/08/2018'),
            array('id' => '17','billing_company_name' => 'EMERSON SRL','billing_registry' => 'J12/88/2006','billing_cif' => 'RO 18284762','billing_address' => 'Str. Emerson, nr. 4, Cluj-Napoca, jud. Cluj','billing_iban' => '-','billing_bank' => '-','billing_capital' => '-','billing_contract_no' => '40','billing_contract_date' => '13/03/2019'),
            array('id' => '20','billing_company_name' => 'PLASTIC OMNIUM AUTO INERGY ROMANIA SRL','billing_registry' => 'J03/216/1999','billing_cif' => 'RO 11909627','billing_address' => 'Str. Depozitelor, nr. 59C, Pitesti, jud. Arges','billing_iban' => 'RO52BRDE030SV05722790300','billing_bank' => 'BRD PITESTI','billing_capital' => '-','billing_contract_no' => '297','billing_contract_date' => '12/12/2014'),
            array('id' => '37','billing_company_name' => 'DELOITTE SHARED SERVICES SRL','billing_registry' => 'J40/11370/2018','billing_cif' => '39721916','billing_address' => 'Sos. Pipera, nr. 46D-46E-48, Cladirea B, Oregon Park, etaj 6, biroul nr. 1, Sector 1, Bucuresti','billing_iban' => '-','billing_bank' => '-','billing_capital' => '-','billing_contract_no' => '101','billing_contract_date' => '28/06/2018'),
            array('id' => '45','billing_company_name' => 'HP Inc Romania SRL','billing_registry' => 'J40/1762/13.02.2015','billing_cif' => 'RO 34104693','billing_address' => 'Str. Fabrica de Glucoza, nr.5, Cladirea F, Etajele P si 8, Sector 2, Bucuresti','billing_iban' => 'RO27CITI0000000724437028','billing_bank' => 'CitiBank Europe plc. Dublin- Romania','billing_capital' => '-','billing_contract_no' => 'PO HPI735743','billing_contract_date' => '26/11/2019'),
            array('id' => '47','billing_company_name' => 'SC VIDAXL CENTER SRL','billing_registry' => 'J40/11966/2014','billing_cif' => 'RO 33701928','billing_address' => 'Str. Dudesti-Pantelimon, nr. 42, et.6, RAMS Center, Sector 3, Bucuresti','billing_iban' => 'RO26INGB0000999904594363','billing_bank' => 'ING Bank','billing_capital' => '-','billing_contract_no' => '35','billing_contract_date' => '21/12/2018'),
            array('id' => '48','billing_company_name' => 'SC LINDE GAZ ROMANIA SRL','billing_registry' => 'J35/1149/1996','billing_cif' => 'RO 8721959','billing_address' => 'Avram Imbroane 9, 300136 Timisoara, jud. Timis','billing_iban' => 'RO46INGB0002001127108912','billing_bank' => 'ING','billing_capital' => '-','billing_contract_no' => '235','billing_contract_date' => '27/09/2013'),
            array('id' => '55','billing_company_name' => 'Deloitte Support Services SRL','billing_registry' => 'J40/17047/2016','billing_cif' => 'RO 36880860','billing_address' => 'Sos. Pipera, Nr. 46D-46E-48, Cladirea B, Etaj 6, Oregon Park, Sector 2, Bucuresti','billing_iban' => 'RO96INGB0001008222578910','billing_bank' => 'ING Bank','billing_capital' => '2.000.000 lei','billing_contract_no' => '25','billing_contract_date' => '25/09/2018'),
            array('id' => '57','billing_company_name' => 'Societatea HRS Recruitment Services SRL','billing_registry' => 'J40/3523/2018','billing_cif' => '39016185','billing_address' => 'Str. Buzesti 50-52, etaj 11, Sector 1, Bucuresti','billing_iban' => 'RO75BACX0000001625650000','billing_bank' => 'Unicredit Bank','billing_capital' => '-','billing_contract_no' => '26','billing_contract_date' => '01/10/2018'),
            array('id' => '63','billing_company_name' => 'UIPATH SRL','billing_registry' => 'J40/8216/2015','billing_cif' => 'RO34737997','billing_address' => 'Str. Vasile Alecsandri, nr. 4 si Str. Daniel Constantinescu, nr.11, Cladirea A, etajele 5 si 6, Sector 1, Bucuresti','billing_iban' => 'RO21CITI0000000798889001','billing_bank' => 'CITIBANK','billing_capital' => '-','billing_contract_no' => '34','billing_contract_date' => '21/12/2018'),
            array('id' => '66','billing_company_name' => 'Hays Specialist Recruitment Romania SRL','billing_registry' => 'J40/1304/2015','billing_cif' => 'RO 34060880','billing_address' => 'Str. Dr. Iacob Felix, etaj 7, Sector 1, Bucuresti','billing_iban' => 'RO26CITI0000000724438016','billing_bank' => 'City Bank Europe plc, Dublin - Romania Branch','billing_capital' => '-','billing_contract_no' => '45','billing_contract_date' => '04/09/2019'),
            array('id' => '69','billing_company_name' => 'HUAWEI TECHNOLOGIES SRL','billing_registry' => 'J40/621/2007','billing_cif' => 'RO 20567140','billing_address' => 'Str. Barbu Vacarescu, nr. 201,  Et. 14, 15, 16, 23, 24 si 2, Sector 2, Bucuresti5','billing_iban' => 'RO23CITI0000000724702007','billing_bank' => 'CITI BANK','billing_capital' => '-','billing_contract_no' => '39','billing_contract_date' => '01/03/2019'),
            array('id' => '76','billing_company_name' => 'IGT Services and Technologies SRL','billing_registry' => 'J40/16484/2017','billing_cif' => 'RO 38273067','billing_address' => 'Str. Oltenitei, nr. 2, et. 1, Sector 4, Bucuresti','billing_iban' => '-','billing_bank' => '-','billing_capital' => '-','billing_contract_no' => '15','billing_contract_date' => '10/04/2019'),
            array('id' => '81','billing_company_name' => 'GLOBAL REMOTE SERVICES SRL','billing_registry' => 'J40/699/21.01.2004','billing_cif' => 'RO 16066508','billing_address' => 'Str. Avrig, nr. 3, etaj 1, sector 2, Bucuresti','billing_iban' => '-','billing_bank' => '-','billing_capital' => '-','billing_contract_no' => '41','billing_contract_date' => '25/04/2019'),
            array('id' => '93','billing_company_name' => 'LUGERA RECRUITMENT SERVICES SRL','billing_registry' => 'J40/2510/2017','billing_cif' => 'RO 37134222','billing_address' => 'Strada Vulturilor, nr. 98, Sector 3, Bucuresti','billing_iban' => 'RO96BTRLRONCRT0384164101','billing_bank' => 'Banca Transilvania','billing_capital' => '-','billing_contract_no' => '44','billing_contract_date' => '05/07/2019'),
            array('id' => '97','billing_company_name' => 'CGI IT ROMANIA SRL','billing_registry' => 'J40/6425/09.04.2008','billing_cif' => 'RO 23681335','billing_address' => 'Soseaua Orhideelor, nr 15 D, The Bridge, Cladirea A/Phase I, etaj 1, Sector 6, Bucuresti','billing_iban' => 'RO15CITI0000000724390005','billing_bank' => 'Citibank','billing_capital' => '-','billing_contract_no' => '31','billing_contract_date' => '13/09/2019'),
            array('id' => '104','billing_company_name' => 'MANPOWER ROMANIA SRL','billing_registry' => 'J40/4299/31.03.2003','billing_cif' => 'RO 15327494','billing_address' => 'Str. Izvor, nr. 80,  Izvor Business Center, etaj 3, Sector 5, Bucuresti','billing_iban' => 'RO72BRDE450SVO1027794500','billing_bank' => 'BRD GSG SMCC','billing_capital' => '-','billing_contract_no' => '47','billing_contract_date' => '31/10/2019'),
            array('id' => '107','billing_company_name' => 'LEASEPLAN SERVICE CENTER SRL','billing_registry' => 'J40/20470/13.12.2017','billing_cif' => 'RO 38597985','billing_address' => 'Splaiul Unirii, nr. 165, Sector 3, Bucuresti','billing_iban' => 'RO57INGB5001008226018910','billing_bank' => 'ING BANK - Sucursala Bucuresti','billing_capital' => '-','billing_contract_no' => '51','billing_contract_date' => '21/01/2020'),
            array('id' => '113','billing_company_name' => 'BUNGE ROMANIA SRL','billing_registry' => 'J10/75/2009','billing_cif' => 'RO 16791351','billing_address' => 'Aleea Industriilor 5-7, jud. Buzau, loc. Buzau','billing_iban' => 'RO96CITI0000000724739008','billing_bank' => 'Citibank Victoriei Bucuresti','billing_capital' => '-','billing_contract_no' => '52','billing_contract_date' => '19/02/2020'),
            array('id' => '114','billing_company_name' => 'GI GROUP STAFFING COMPANY SRL','billing_registry' => 'J40/6080/2009','billing_cif' => 'RO 25578361','billing_address' => 'Str. Arh. Louis Blanc, nr. 1, Etaj 5, Sector 1, Bucuresti','billing_iban' => 'RO32WBAN004124842932RO01','billing_bank' => 'INTESA SANPAOLO ROMANIA, Suc. Victoria','billing_capital' => '9.736.000 lei','billing_contract_no' => '53','billing_contract_date' => '20/02/2020'),
            array('id' => '115','billing_company_name' => 'SCC SERVICES ROMANIA SRL','billing_registry' => 'J22/823/2006','billing_cif' => '18544528','billing_address' => 'Soseaua Pacurari, nr. 138, Cladirea Ideo, Etaj 1, Birou B1, jud. Iasi, loc. iasi','billing_iban' => 'RO46CITI0000000709524003','billing_bank' => 'CITIBANK EUROPE PLC, DUBLIN -SUC ROMANIA','billing_capital' => '-','billing_contract_no' => '253','billing_contract_date' => '10/02/2020'),
            array('id' => '117','billing_company_name' => 'SPARKWARE TECHNOLOGIES SRL','billing_registry' => 'J40/1818/2013','billing_cif' => 'RO31225771','billing_address' => 'Bd. Vasile Milea, nr.4E, et. 8, Afi Park1, Sector 6, Bucuresti','billing_iban' => 'RO85INGB0000999903474018','billing_bank' => 'ING BANK','billing_capital' => '-','billing_contract_no' => '54','billing_contract_date' => '04/03/2020')
        );
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        foreach ($this->clients as $client) {
            try {
                DB::table("clients")
                    ->where('id', $client['id'])
                    ->update($client);
            } catch (Exception $e) {

            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
