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
        $std->cUF = substr($_POST['ibge_cidade'],0,2); //codigo ibge da UF será sempre os 2 primeiros digitos do ibge da cidade
        $std->cNF = '80070008'; //código numérico que compõe a chave de acesso (deveria ser aleatorio, mas não tem problema ser fixo)
        $std->natOp = 'VENDA'; // descrição da natureza da operação
        $std->mod = 55; // código do modelo do documento fiscal
        $std->serie = 1; // série do documento fiscal
        $std->nNF = $_POST['numero']; // número do documento fiscal
        $std->dhEmi = date("c");; // data de emissão do documento fiscal
        $std->dhSaiEnt = date("Y-m-d\TH:i:sP");; // data de saída ou da entrada da mercadoria / produto
        $std->tpNF = 1; // tipo de operação
        $std->idDest = 1;
        $std->cMunFG = $_POST['ibge_cidade']; //Código de município precisa ser válido
        $std->tpImp = 1; // formato de impressão do DANFE
        $std->tpEmis = 1; // se informada a tag de tpemis=1 dhcont e xjust não devem ser informados, se informada dhcont e xjust devem ser informados.
        $std->cDV = calculaDV($std->cUF, date('y'), date('m'), $_POST['cnpj'], $std->serie, $std->nNF, $std->tpEmis, $std->cNF); // digito verificado da chave de acesso da nf-e 
        $std->tpAmb = 2; // Se deixar o tpAmb como 2 você emitirá a nota em ambiente de homologação(teste) e as notas fiscais aqui não tem valor fiscal
        $std->finNFe = 1; // finalidade de emissão da NF-e
        $std->indFinal = 1; // Indica se esta NF-e foi emitida para Consumidor Final ou não ( por exemplo) para Revenda. 1 = Consumidor final
        $std->indPres = 2; // Indiica se o destinatario da NF-e estava presente na emissão. 2 = Não presencial, pela internet
        $std->procEmi = 0; // processo de emissão da NF-e. 0 = emissão de NF-e com aplicativo do contribuinte
        $std->verProc = '1.0'; // versão do processo de emissão da nf-e
        $nfe->tagide($std);

        $std = new stdObject();
        $std->xNome = $_POST['razao']; // razão social ou nome do emitente
        $std->IE = $_POST['inscricao'];  //IE ??? campo de informação obrigatória nos casos de emissão própria (procEmi = 0,2 ou 3). A IE deve ser informada apenas com algarismos para destinatários contribuientes do ICMS, sem caracteres de formatação (ponto,barra,hifen, etc.); O literal "ISENTO" deve ser informado apenas para contribuintes do ICMS que são isentos de inscrição no cadastro de contribuintes do ICMS e estejam emitindo NF-e avulsa;
        $std->CRT = 3; // código do regime tributário 1- simples nacional , 2 - simples nacional - excesso de sublimite de receita bruta, 3 - regime normal (v2.0)
        $std->CNPJ = $_POST['cnpj']; // cnpj da empresa
        $nfe->tagemit($std);

        $std = new stdObject();
        $std->xLgr = $_POST['endereco']; // logradouro da empresa
        $std->nro = $_POST['endereco_numero']; // número
        $std->xBairro = $_POST['bairro']; // bairro
        $std->cMun = $_POST['ibge_cidade']; //Código de município precisa ser válido e igual o  cMunFG
        $std->xMun = $_POST['cidade']; // nome do municipio
        $std->UF = ibge_sigla(substr($_POST['ibge_cidade'],0,2)); // sigla da uf 
        $std->CEP = $_POST['cep']; // código do cep
        $std->cPais = '1058'; // código do país
        $std->xPais = 'BRASIL'; // nome do país
        $nfe->tagenderEmit($std);

        $std = new stdObject();
        $std->xNome = $_POST['destinatario']; // razão social ou nome do destinatário
        $std->indIEDest = 9; // indica se é contribuinte ou não. PF sao sempre não contribuintes e PJ pode ser ambos. 9 = Não contribuinte
        $std->CPF = $_POST['cpf_destinatario']; // cpf do destinatário
        $nfe->tagdest($std);

        $std = new stdObject();
        $std->xLgr = $_POST['endereco_destinatario']; // logradouro da empresa destinatario
        $std->nro = $_POST['numero_endereco_destinatario']; // numero da empresa destinatario
        $std->xBairro = $_POST['bairro_destinatario']; // bairro da empresa destinatario
        $std->cMun = $_POST['ibge_cidade_destinatario']; // codigo do municipio
        $std->xMun = $_POST['cidade_destinatario']; // nome do municipio
        $std->UF = ibge_sigla(substr($_POST['ibge_cidade_destinatario'],0,2));; // sigla da uf 
        $std->CEP = $_POST['cep_destinatario']; // código do cep
        $std->cPais = '1058'; // código do país
        $std->xPais = 'BRASIL'; // nome do país
        $nfe->tagenderDest($std);

        $std = new stdObject();
        $std->item = 1; // numero do ben
        $std->cEAN = 'SEM GTIN'; // GTIN do produto, antigo código ean ou código de barras // preencher com cfop, caso se trate de itens não relacionados com mercadorias / produtos e que o contribuinte não possua codificação própria. Formato "CFOP9999"
        $std->cEANTrib = 'SEM GTIN'; // gtin da unidade tributável, antigo código ean ou código de barras
        $std->cProd = $_POST['codigo_produto']; // código do produto ou serviço
        $std->xProd = $_POST['nome_produto']; // descrição do produto ou serviço
        $std->NCM = $_POST['ncm']; // códigio ncm com 8 dígitos ou 2 digitos (gênero) / codigo ncm (8 posicoes) informar o genero (posição do capitulo do NCM) quando a operação não for de comércio exterior (importação / exportação) ou o produto não seja tributado pelo IPI. Em caso de serviço informar o código 99 (v2.0)
        $std->CFOP = $_POST['cfop']; // código fiscal de operações e prestações / utilizar tabela de CFOP.
        $std->uCom = $_POST['unidade_medida']; // unidade comercial / informar a unidade de comercialização do produto.
        $std->qCom = $_POST['quantidade']; // quantidade comercial / informar a quantidade de comercialização do produto (v2.0)
        $std->vUnCom = $_POST['valor_unitario']; // valor unitário de comercialização / informar o valor unitário de comercialização do produto campo meramente informativo, o contribuinte pode utilizar a precisão desejada (0-10 decimais). Para efeitos de cálculo, o valor unitário será obtido pela divisão do valor do produto pela quantidade comercial. (v2.0)
        $std->vProd = $std->qCom * $std->vUnCom; // valor total bruto dos produtos ou serviços
        $valor = $std->vProd;
        $std->uTrib = $std->uCom; // unidade tributável ?
        $std->qTrib = $std->qCom; // quantidade tributável
        $std->vUnTrib = $std->vUnCom; // valor unitário de tributação / informar o valor do produto, campo meramente informativo, o contribuinte pode utilizar a precisão desejada (0-10 decimais). Para efeitos de cálculo, o valor unitário será obtido pela divisão do valor do produto pela quantidade tributável.
        $std->indTot = 1; // indica se valor do item (vProd) entra no valor total da NF-e (vProd) / 0 - o valor do item (vProd) não compõe o valor total da NF-e (vProd), 1 - o valor do item (vProd)  compõe o valor total da NF-e (vProd) (v2.0)
        $nfe->tagprod($std);

        $std = new stdObject();
        $std->item = 1; // ?
        $std->vTotTrib = $_POST['valor_aprox_tributos']; // Tributação aproximada do item
        $nfe->tagimposto($std);

        $std = new stdObject();
        $std->item = 1; // ?
        $std->orig = 0; // origem da mercadoria / 0 - nacional, 1 - estrangeira - importação direta, 2 - estrangeira - adquirida no mercado interno.
        $std->CST = $_POST['cst']; // tributação do ICMS / 00 - tributada integralmente
        $std->vBC = $valor; // valor da BC do ICMS
        $std->pICMS = $_POST['aliq_icms']; // alíquota do imposto
        $std->vICMS = $std->vBC * $std->pICMS/100; // valor do icms
        $icms = $std->vICMS ;
        $nfe->tagICMS($std);

        $std = new stdObject();
        $std->item = 1;
        $std->CST = '01';
        $std->vBC = $valor;
        $std->pPIS = $_POST['aliq_pis'];
        $std->vPIS = $std->vBC * $std->pPIS/100;
        $pis = $std->vPIS ;
        $nfe->tagPIS($std);

        $std = new stdObject();
        $std->item = 1;
        $std->CST = '01';
        $std->vBC = $valor;
        $std->pCOFINS = $_POST['aliq_cofins'];
        $std->vCOFINS = $std->vBC * $std->pCOFINS/100;
        $cofins = $std->vCOFINS;
        $nfe->tagCOFINS($std);

        //Somatorias dos valores de todos os itens da NFe
        $std = new stdObject();
        $std->vBC = $valor;
        $std->vICMS = $icms;
        $std->vICMSDeson = 0.00;
        $std->vBCST = 0.00;
        $std->vST = 0.00;
        $std->vProd = $valor;
        $std->vFrete = 0.00;
        $std->vSeg = 0.00;
        $std->vDesc = 0.00;
        $std->vII = 0.00;
        $std->vIPI = 0.00;
        $std->vPIS = $pis;
        $std->vCOFINS = $cofins;
        $std->vOutro = 0.00;
        $std->vNF = $std->vProd + $std->vST + $std->vFrete + $std->vSeg - $std->vDesc + $std->vIPI + $std->vII + $std->vOutro;
        $valor = $std->vNF;
        $std->vTotTrib = $_POST['valor_aprox_tributos'];
        $nfe->tagICMSTot($std);

        $std = new stdObject();
        $std->nFat = '001';
        $std->vOrig = $valor;
        $std->vLiq = $valor;
        $nfe->tagfat($std);

        $std = new stdObject();
        $std->vTroco = 0;
        $nfe->tagpag($std);

        $std = new stdObject();
        $std->indPag = 0;
        $std->tPag = "01";
        $std->vPag = $valor;
        $std->indPag=0;
        $nfe->tagdetPag($std);

        return $nfe->getXML();
    }

    private function compoeChaveAcesso43($cUF, $ano, $mes, $cnpj, $serie, $numero, $tpEmi, $cNF )
    {
        //Atribui os zeros a esquerda, caso não existam
        $serie = str_pad($serie, 3, "0", STR_PAD_LEFT);
        $numero = str_pad($numero, 9, "0", STR_PAD_LEFT);
        $cNF = str_pad($cNF, 8, "0", STR_PAD_LEFT);
        
        //Remove formatação e deixa apenas os numeros
        $cnpj = preg_replace("/[^0-9]/", "",$cnpj);

        return $cUF . $ano . $mes . $cnpj . "55" . $serie . $numero . $tpEmi . $cNF;
    }

    private function calculaDV($cUF, $ano, $mes, $cnpj, $serie, $numero, $tpEmi, $cNF)
    {
        return calculaDV(compoeChaveAcesso43($cUF, $ano, $mes, $cnpj, $serie, $numero, $tpEmi, $cNF));
    }

    private function calculaDV($chave43)
    {
        $mult = array(2, 3, 4, 5, 6, 7, 8, 9);
        $count = 42;
        $soma = 0;
        while ($count >= 0) 
        {
            for ($i = 0; $i < count($mult) && $count >= 0; $i++) 
            {
                $num = (int) substr($chave43, $count, 1);
                $peso = (int) $mult[$i];
                $soma += $num * $peso;
                $count--;
            }
        }
        $resto = $soma % 11;
        
        if ($resto == '0' || $resto == '1') 
            $cDV = 0;
        else 
            $cDV = 11 - $resto;
        
        return (string) $cDV;
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
