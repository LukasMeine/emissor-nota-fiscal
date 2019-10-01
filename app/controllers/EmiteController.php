<?php
namespace NotaFiscal\Controllers;

require_once('../vendor/autoload.php');
session_start();

use NFePHP\NFe\Make;
use NFePHP\Extras\Danfe as Danfe;
use NFePHP\Common\Files\FilesFolders;
use NFePHP\NFe\Convert;
use NFePHP\NFe\Tools as tools;
use NFePHP\Common\Certificate as certificate;
use NFePHP\NFe\Common\Standardize as standart;
use \stdClass as stdObject;

class EmiteController extends ControllerBase
{
    private function getTools()
    {
        $certificadoDigital = file_get_contents($_FILES["certificado"]["tmp_name"]);
        return new tools($this->gera_json(), certificate::readPfx($certificadoDigital, $_POST['senha']));
    }

    public function indexAction()
    {
        try {
            $idLote = str_pad(100, 15, '0', STR_PAD_LEFT); // Identificador do lote

            $resp = $this->getTools()->sefazEnviaLote([$this->xmlAssinado()], $idLote);

            $st = new standart();
            $std = $st->toStd($resp);
            if ($std->cStat != 103) {
                //erro registrar e voltar
                exit("[$std->cStat] $std->xMotivo");
            }

            $recibo = $std->infRec->nRec;

            $recibo = $this->consulta_recibo($recibo);

            if ($recibo['situacao'] == "autorizada") {

                //Só imprimimos a DANFE caso ela esteja autorizada.
                $this->pdf_nota($this->xmlAssinado());
            } else {
                //Caso contrário, imprimimos detalhes sobre a rejeição
                print_r($recibo);
            }
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    private function ibge_sigla($ibge)
    {
        switch ($ibge) {
            case 11: return "RO";
            case 12: return "AC";
            case 13: return "AM";
            case 14: return "RR";
            case 15: return "PA";
            case 16: return "AP";
            case 17: return "TO";
            
            case 21: return "MA";
            case 22: return "PI";
            case 23: return "CE";
            case 24: return "RN";
            case 25: return "PB";
            case 26: return "PE";
            case 27: return "AL";
            case 28: return "SE";
            case 29: return "BA";

            case 31: return "MG";
            case 32: return "ES";
            case 33: return "RJ";
            case 35: return "SP";

            case 41: return "PR";
            case 42: return "SC";
            case 43: return "RS";

            case 50: return "MS";
            case 51: return "MT";
            case 52: return "GO";
            case 53: return "DF";

            default: throw new Exception('IBGE da UF inválido.');
        }
    }


    private function get_xml()
    {
        $nfe = new Make();
        $std = new stdObject();

        $std->versao = '4.00'; // versão do leiaute
        $std->Id = null; // identificador da TAG a ser assinada
        $std->pk_nItem = ''; // regra para que a numeração do item de detalhe da NF-e seja única.
        $nfe->taginfNFe($std);

        $std = new stdObject();
        $std->cUF = 53; //coloque um código real e válido
        $std->cNF = '80070008'; //código numérico que compõe a chave de acesso
        $std->natOp = 'VENDA'; // descrição da natureza da operação
        $std->mod = 55; // código do modelo do documento fiscal
        $std->serie = 1; // série do documento fiscal
        $std->nNF = $_POST['numero']; // número do documento fiscal
        $std->dhEmi = '2018-09-17T20:48:00-02:00'; // data de emissão do documento fiscal
        $std->dhSaiEnt = '2018-09-17T20:48:00-02:00'; // data de saída ou da entrada da mercadoria / produto
        $std->tpNF = 1; // tipo de operação
        $std->idDest = 1;
        $std->cMunFG = 5300108; //Código de município precisa ser válido
        $std->tpImp = 1; // formato de impressão do DANFE
        $std->tpEmis = 1; // se informada a tag de tpemis=1 dhcont e xjust não devem ser informados, se informada dhcont e xjust devem ser informados.
        $std->cDV = 2; // digito verificado da chave de acesso da nf-e
        $std->tpAmb = 2; // Se deixar o tpAmb como 2 você emitirá a nota em ambiente de homologação(teste) e as notas fiscais aqui não tem valor fiscal
        $std->finNFe = 1; // finalidade de emissão da NF-e
        $std->indFinal = 0; // ?
        $std->indPres = 0; // ?
        $std->procEmi = '0'; // processo de emissão da NF-e
        $std->verProc = 1; // versão do processo de emissão da nf-e
        $nfe->tagide($std);

        $std = new stdObject();
        $std->xNome = $_POST['razao']; // razão social ou nome do emitente
        $std->IE = $_POST['inscricao'];  //IE ??? campo de informação obrigatória nos casos de emissão própria (procEmi = 0,2 ou 3). A IE deve ser informada apenas com algarismos para destinatários contribuientes do ICMS, sem caracteres de formatação (ponto,barra,hifen, etc.); O literal "ISENTO" deve ser informado apenas para contribuintes do ICMS que são isentos de inscrição no cadastro de contribuintes do ICMS e estejam emitindo NF-e avulsa;
        $std->CRT = 3; // código do regime tributário 1- simples nacional , 2 - simples nacional - excesso de sublimite de receita bruta, 3 - regime normal (v2.0)
        $std->CNPJ = $_POST['cnpj']; // cnpj da empresa
        $nfe->tagemit($std);

        $std = new stdObject();
        $std->xLgr = "Rua Teste"; // logradouro da empresa
        $std->nro = '203'; // número
        $std->xBairro = 'Centro'; // bairro
        $std->cMun = 5300108; //Código de município precisa ser válido e igual o  cMunFG
        $std->xMun = 'Bauru'; // nome do municipio
        $std->UF = 'DF'; // sigla da uf
        $std->CEP = '80045190'; // código do cep
        $std->cPais = '1058'; // código do país
        $std->xPais = 'BRASIL'; // nome do país
        $nfe->tagenderEmit($std);

        $std = new stdObject();
        $std->xNome = 'Empresa destinatário teste'; // razão social ou nome do destinatário
        $std->indIEDest = 2; // ???
        $std->IE = 'ISENTO'; //IE ??? campo de informação obrigatória nos casos de emissão própria (procEmi = 0,2 ou 3). A IE deve ser informada apenas com algarismos para destinatários contribuientes do ICMS, sem caracteres de formatação (ponto,barra,hifen, etc.); O literal "ISENTO" deve ser informado apenas para contribuintes do ICMS que são isentos de inscrição no cadastro de contribuintes do ICMS e estejam emitindo NF-e avulsa;
        $std->CNPJ = '15236260000138'; // cnpj do destinatário ou cpf
        $nfe->tagdest($std);

        $std = new stdObject();
        $std->xLgr = "Rua Teste"; // logradouro da empresa destinatario
        $std->nro = '203'; // numero da empresa destinatario
        $std->xBairro = 'Centro'; // bairro da empresa destinatario
        $std->cMun = '5300108'; // codigo do municipio
        $std->xMun = 'Bauru'; // nome do municipio
        $std->UF = 'DF'; // sigla da uf
        $std->CEP = '80045190'; // código do cep
        $std->cPais = '1058'; // código do país
        $std->xPais = 'BRASIL'; // nome do país
        $nfe->tagenderDest($std);

        $std = new stdObject();
        $std->item = 1; // numero do ben
        $std->cEAN = 'SEM GTIN'; // GTIN do produto, antigo código ean ou código de barras // preencher com cfop, caso se trate de itens não relacionados com mercadorias / produtos e que o contribuinte não possua codificação própria. Formato "CFOP9999"
        $std->cEANTrib = 'SEM GTIN'; // gtin da unidade tributável, antigo código ean ou código de barras
        $std->cProd = '0001'; // código do produto ou serviço
        $std->xProd = 'Produto teste'; // descrição do produto ou serviço
        $std->NCM = '84669330'; // códigio ncm com 8 dígitos ou 2 digitos (gênero) / codigo ncm (8 posicoes) informar o genero (posição do capitulo do NCM) quando a operação não for de comércio exterior (importação / exportação) ou o produto não seja tributado pelo IPI. Em caso de serviço informar o código 99 (v2.0)
        $std->CFOP = '5102'; // código fiscal de operações e prestações / utilizar tabela de CFOP.
        $std->uCom = 'PÇ'; // unidade comercial / informar a unidade de comercialização do produto.
        $std->qCom = '1.0000'; // quantidade comercial / informar a quantidade de comercialização do produto (v2.0)
        $std->vUnCom = '10.99'; // valor unitário de comercialização / informar o valor unitário de comercialização do produto campo meramente informativo, o contribuinte pode utilizar a precisão desejada (0-10 decimais). Para efeitos de cálculo, o valor unitário será obtido pela divisão do valor do produto pela quantidade comercial. (v2.0)
        $std->vProd = '10.99'; // valor total bruto dos produtos ou serviços
        $std->uTrib = 'PÇ'; // unidade tributável ?
        $std->qTrib = '1.0000'; // quantidade tributável
        $std->vUnTrib = '10.99'; // valor unitário de tributação / informar o valor do produto, campo meramente informativo, o contribuinte pode utilizar a precisão desejada (0-10 decimais). Para efeitos de cálculo, o valor unitário será obtido pela divisão do valor do produto pela quantidade tributável.
        $std->indTot = 1; // indica se valor do item (vProd) entra no valor total da NF-e (vProd) / 0 - o valor do item (vProd) não compõe o valor total da NF-e (vProd), 1 - o valor do item (vProd)  compõe o valor total da NF-e (vProd) (v2.0)
        $nfe->tagprod($std);

        $std = new stdObject();
        $std->item = 1; // ?
        $std->vTotTrib = 10.99; // talvez seja m01 grupo de tributos incidentes no produto ou serviço
        $nfe->tagimposto($std);

        $std = new stdObject();
        $std->item = 1; // ?
        $std->orig = 0; // origem da mercadoria / 0 - nacional, 1 - estrangeira - importação direta, 2 - estrangeira - adquirida no mercado interno.
        $std->CST = '00'; // tributação do ICMS / 00 - tributada integralmente
        $std->modBC = 0; // modalidade de determinação da BC do icms / 0 - margem valor agregador (%), 1 - pauta (valor), 2 - preço tabelado máximo (valor), 3 - valor da operação
        $std->vBC = '0.20'; // valor da BC do ICMS
        $std->pICMS = '18.0000'; // alíquota do imposto
        $std->vICMS = '0.04'; // valor do icms
        $nfe->tagICMS($std);

        $std = new stdObject();
        $std->item = 1;
        $std->cEnq = '999'; // código de enquadramento legal do IPI / tabela a ser criada pela RFB, informar 999 enquanto a tabela não for criada.
        $std->CST = '50'; // tributação do ICMS 40 - isenta, 41 - não tributada, 50 - suspensão
        $std->vIPI = 0; // valor do IPI
        $std->vBC = 0; // valor da BC do ICMS
        $std->pIPI = 0; // ?
        $nfe->tagIPI($std);

        $std = new stdObject();
        $std->item = 1;
        $std->CST = '07';
        $std->vBC = 0;
        $std->pPIS = 0;
        $std->vPIS = 0;
        $nfe->tagPIS($std);

        $std = new stdObject();
        $std->item = 1;
        $std->vCOFINS = 0;
        $std->vBC = 0;
        $std->pCOFINS = 0;

        $nfe->tagCOFINSST($std);

        $std = new stdObject();
        $std->item = 1;
        $std->CST = '01';
        $std->vBC = 0;
        $std->pCOFINS = 0;
        $std->vCOFINS = 0;
        $std->qBCProd = 0;
        $std->vAliqProd = 0;
        $nfe->tagCOFINS($std);

        $std = new stdObject();
        $std->vBC = '0.20';
        $std->vICMS = 0.04;
        $std->vICMSDeson = 0.00;
        $std->vBCST = 0.00;
        $std->vST = 0.00;
        $std->vProd = 10.99;
        $std->vFrete = 0.00;
        $std->vSeg = 0.00;
        $std->vDesc = 0.00;
        $std->vII = 0.00;
        $std->vIPI = 0.00;
        $std->vPIS = 0.00;
        $std->vCOFINS = 0.00;
        $std->vOutro = 0.00;
        $std->vNF = 11.03;
        $std->vTotTrib = 0.00;
        $nfe->tagICMSTot($std);


        $std = new stdObject();
        $std->modFrete = 1;
        $nfe->tagtransp($std);

        $valor = rand(0, 1000);

        $std = new stdObject();
        $std->item = 1;
        $std->qVol = 2;
        $std->esp = 'caixa';
        $std->marca = 'OLX';
        $std->nVol = '11111';
        $std->pesoL = 10.00;
        $std->pesoB = 11.00;
        $nfe->tagvol($std);

        $std = new stdObject();
        $std->nFat = '002';
        $std->vOrig = $valor;
        $std->vLiq = $valor;
        $nfe->tagfat($std);

        $std = new stdObject();
        $std->nDup = '001';
        $std->dVenc = date('Y-m-d');
        $std->vDup = $valor;
        $nfe->tagdup($std);

        $std = new stdObject();
        $std->vTroco = 0;
        $nfe->tagpag($std);

        $std = new stdObject();
        $std->indPag = 0;
        $std->tPag = "01";
        $std->vPag = 10.99;
        $std->indPag=0;
        $nfe->tagdetPag($std);

        return $nfe->getXML();
    }

    private function gera_json()
    {
        $config = [
            "atualizacao" => "2018-09-17 06:01:21",
            "tpAmb" => 2,
            "razaosocial" => "Fake Materiais de construção Ltda",
            "siglaUF" => "DF",
            "cnpj" => "26463519000108",
            "schemes" => "PL_008i2",
            "versao" => "4.00",
            "tokenIBPT" => "AAAAAAA",
            "CSC" => "GPB0JBWLUR6HWFTVEAS6RJ69GPCROFPBBB8G",
            "CSCid" => "000002",
            "aProxyConf" => [
                "proxyIp" => "",
                "proxyPort" => "",
                "proxyUser" => "",
                "proxyPass" => ""
            ]
        ];

        return json_encode($config);
    }

    private function xmlAssinado()
    {
        try {
            return $xmlAssinado = $this->getTools()->signNFe($this->get_xml()); // O conteúdo do XML assinado fica armazenado na variável $xmlAssinado
        } catch (\Exception $e) {
            //aqui você trata possíveis exceptions da assinatura
            exit($e->getMessage());
        }
    }

    private function consulta_recibo($recibo = null)
    {
        try {
            $tools = $this->getTools();
            $xmlResp = $tools->sefazConsultaRecibo($recibo);
            $st = new standart();
            $std = $st->toStd($xmlResp);
            if ($std->cStat=='103') {
                //lote enviado mas ainda não foi precessado pela SEFAZ;
            }
            if ($std->cStat=='105') {
                //lote em processamento tente novamente mais tarde
            }

            if ($std->cStat=='104') {
                //lote processado (tudo ok)
                if ($std->protNFe->infProt->cStat=='100') {
                    //Autorizado o uso da NF-e
                    $return = ["situacao"=>"autorizada",
                       "numeroProtocolo"=>$std->protNFe->infProt->nProt,
                       "xmlProtocolo"=>$xmlResp];
                } elseif (in_array($std->protNFe->infProt->cStat, ["302"])) {
                    //DENEGADAS
                    $return = ["situacao"=>"denegada",
                       "numeroProtocolo"=>$std->protNFe->infProt->nProt,
                       "motivo"=>$std->protNFe->infProt->xMotivo,
                       "cstat"=>$std->protNFe->infProt->cStat,
                       "xmlProtocolo"=>$xmlResp];
                } else {
                    //não autorizada (rejeição)
                    $return = ["situacao"=>"rejeitada",
                       "motivo"=>$std->protNFe->infProt->xMotivo,
                       "cstat"=>$std->protNFe->infProt->cStat];
                }
            }//104
            else {
                //outros erros possíveis
                $return = ["situacao"=>"rejeitada",
                   "motivo"=>$std->xMotivo,
                   "cstat"=>$std->cStat];
            }

            return $return;
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }


    private function pdf_nota($xml)
    {
        //Imprime a DANFE na tela.
        $danfe = new Danfe($xml, 'P', 'A4', '', 'I', '');
        $id = $danfe->montaDANFE();

        $danfe->printDANFE($id.'.pdf', 'F');
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=filename.pdf");
        @readfile("../public/{$id}.pdf");
    }
}