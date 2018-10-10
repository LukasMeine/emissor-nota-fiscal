# Emissor de notas fiscais de propósito genérico

> API de emissão de notas fiscais construída em cima da sped-nfe. Feita com Phalcon PHP para oferecer altíssima performance 🚀🚀

![emissor](https://user-images.githubusercontent.com/20716798/46682482-0902b100-cbc4-11e8-9301-7869c012d617.gif)


## Breve resumo e objetivo do projeto

O objetivo deste projeto é ser um microsserviço enxuto de emissão de notas fiscais. Emitir notas fiscais é um trabalho muito complicado para um programador, e existem poucas alternativas open source pra isso. Nossa ideia é simplificar esse processo para que qualquer um possa emitir suas notas fiscais sem ter muito trabalho 😆

Para tornar isso realidade, nós construimos uma API de exemplo utilizando o projeto [Sped-nfe](https://github.com/nfephp-org/sped-nfe) e consultamos um contador para entendermos o que é **cada um dos parâmetros que essa biblioteca usa**, afinal um programador sabe programar, e não jargão técnico de ciências contábeis. 

Assim que você instalar o projeto, ele estará acessível na porta 80 (localhost) e você verá alguns parâmetros por lá, como CNPJ, Inscrição estadual, Razão social e certificado digital. Ainda existem MUITOS outros parâmetros que precisam ser configurados, mas nós não fizemos front end pra isso. Inclusive, convidamos vocês a contribuirem nisso ✌ 

Para configurar os outros parâmetros, entre no arquivo [app/controllers/EmiteController.php](https://github.com/citaralabs/emissor-nota-fiscal/blob/master/app/controllers/EmiteController.php) e edite os parâmetros que for necessário. Você encontrará uma breve explicação sobre a maioria deles em forma de comentário, por exemplo:

```
$std->cEAN = 'SEM GTIN'; // GTIN do produto, antigo código ean ou código de barras // preencher com cfop, caso se trate de itens não relacionados com mercadorias / produtos e que o contribuinte não possua codificação própria. Formato "CFOP9999"
$std->cEANTrib = 'SEM GTIN'; // gtin da unidade tributável, antigo código ean ou código de barras
$std->cProd = '0001'; // código do produto ou serviço
$std->xProd = 'Produto teste'; // descrição do produto ou serviço
$std->NCM = '84669330'; // códigio ncm com 8 dígitos ou 2 digitos (gênero) / codigo ncm (8 posicoes) informar o genero (posição do capitulo do NCM) quando a operação não for de comércio exterior (importação / exportação) ou o produto não seja tributado pelo IPI. Em caso de serviço informar o código 99 (v2.0)
$std->CFOP = '5102'; // código fiscal de operações e prestações / utilizar tabela de CFOP.
```

## Live preview

Nós não hospedamos uma live preview porque este projeto exige que seja passado como parâmetro o **certificado digital e senha** da sua empresa. Nós achamos que vocês não confiariam em mandar isso para os nossos servidores. ( Nós definitivamente não confiaríamos 
👀  )

## Requerimentos

- Apache
 
- Phalcon PHP
 
- PHP 7.2.x

- Composer

## Instalando e fazendo deploy em produção

- Instale o Apache, PHP 7.2.x, Phalcon PHP e composer

- Habilite a extensão SOAP no apache

- Clone este repositório

- Composer install

## Documentação

> Ainda não fizemos uma documentação da API. Inclusive, convidamos vocês a nos ajudar com isso 👌

## Construido com

![citaralab](https://avatars1.githubusercontent.com/u/1221505?s=200&v=4)

*O framework Phalcon PHP*

E muito ❤ por Citara Labs

