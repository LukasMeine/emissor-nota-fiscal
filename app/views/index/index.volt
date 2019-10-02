<html><head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/vendifier-nfe/public/assets/styles.css">
    <title>Emissor de nota fiscal</title>
</head>

<body>
    <div class="container" id="advanced-search-form">
        <h2>Emissor de nota fiscal</h2>
        <form action="emite" method="POST" target="_blank" enctype="multipart/form-data">
            <div class="form-group">
                <label for="first-name">Razão Social</label>
                <input type="text" class="form-control" placeholder="Razão Social" name="razao" id="razao">
            </div>
            <div class="form-group">
                <label for="last-name">Inscrição Estadual</label>
                <input type="text" class="form-control" placeholder="Inscrição Estadual" name="inscricao" id="inscricao">
            </div>
            <div class="form-group">
                <label for="country">CNPJ</label>
                <input type="text" class="form-control" name="cnpj" placeholder="CNPJ" id="cnpj">
            </div>
            <div class="form-group">
                <label for="first-name">Endereço</label>
                <input type="text" class="form-control" placeholder="Endereço" name="endereco" id="endereco">
            </div>
            <div class="form-group">
                <label for="first-name">Nº endereço</label>
                <input type="text" class="form-control" placeholder="Nº endereço" name="numero_endereco" id="numero_endereco">
            </div>            
            <div class="form-group">
                <label for="first-name">Bairro</label>
                <input type="text" class="form-control" placeholder="Bairro" name="bairro" id="bairro">
            </div>            
            <div class="form-group">
                <label for="first-name">IBGE cidade</label>
                <input type="number" class="form-control" placeholder="IBGE cidade" name="ibge_cidade" id="ibge_cidade">
            </div>            
            <div class="form-group">
                <label for="first-name">Nome cidade</label>
                <input type="text" class="form-control" placeholder="Nome cidade" name="cidade" id="cidade">
            </div>   
            <div class="form-group">
                <label for="first-name">CEP</label>
                <input type="number" class="form-control" placeholder="CEP" name="cep" id="cep">
            </div>            
            <div class="form-group">
                <label for="number">Certificado digital</label>
                <input type="file" class="form-control"name="certificado" id="certificado">
            </div>
            <div class="form-group">
                <label for="age">Senha do certificado Digital</label>
                <input type="text" class="form-control" placeholder="Senha do certificado Digital" name="senha" id="senha">
            </div>
            
            <div class="form-group">
                <label for="age">Número da nota</label>
                <input type="text" class="form-control" placeholder="Número da nota" name="numero" name="numero" id="numero">
            </div>
            
            <div class="form-group">
                <label for="first-name">Nome destinatário</label>
                <input type="text" class="form-control" placeholder="Nome destinatário" name="nome_destinatario" id="nome_destinatario">
            </div>
            <div class="form-group">
                <label for="country">CPF destinatário</label>
                <input type="text" class="form-control" name="cpf_destinatario" placeholder="CPF" id="cpf_destinatario">
            </div>
            <div class="form-group">
                <label for="first-name">Endereço destinatário</label>
                <input type="text" class="form-control" placeholder="Endereço destinatário" name="endereco_destinatario" id="endereco_destinatario">
            </div>
            <div class="form-group">
                <label for="first-name">Nº endereço destinatário</label>
                <input type="text" class="form-control" placeholder="Nº endereço" name="numero_endereco_destinatario" id="numero_endereco_destinatario">
            </div>            
            <div class="form-group">
                <label for="first-name">Bairro destinatário</label>
                <input type="text" class="form-control" placeholder="Bairro destinatário" name="bairro_destinatario" id="bairro_destinatario">
            </div>            
            <div class="form-group">
                <label for="first-name">IBGE cidade destinatário</label>
                <input type="number" class="form-control" placeholder="IBGE cidade destinatário" name="ibge_cidade_destinatario" id="ibge_cidade_destinatario">
            </div>            
            <div class="form-group">
                <label for="first-name">Nome cidade destinatário</label>
                <input type="text" class="form-control" placeholder="Nome cidade destinatário" name="cidade_destinatario" id="cidade_destinatario">
            </div>            
            <div class="form-group">
                <label for="first-name">CEP destinatário</label>
                <input type="number" class="form-control" placeholder="CEP destinatário" name="cep_destinatario" id="cep_destinatario">
            </div>       
            
            <!-- Campos do produto-->
            <div class="form-group">
                <label for="first-name">Código Produto</label>
                <input type="text" class="form-control" placeholder="Código do Produto" name="cod_produto" id="cod_produto">
            </div>   
            <div class="form-group">
                <label for="first-name">Nome Produto</label>
                <input type="text" class="form-control" placeholder="Nome do Produto" name="nome_produto" id="nome_produto">
            </div>   
            <div class="form-group">
                <label for="first-name">NCM</label>
                <input type="text" class="form-control" placeholder="NCM" name="ncm" id="ncm">
            </div>   
            <div class="form-group">
                <label for="first-name">Unidade Medida</label>
                <input type="text" class="form-control" placeholder="Unidade de Medida" name="unidade_medida" id="unidade_medida">
            </div>   
            <div class="form-group">
                <label for="first-name">Quantidade</label>
                <input type="text" class="form-control" placeholder="Quantidade" name="quantidade" id="quantidade">
            </div>   
            <div class="form-group">
                <label for="first-name">Valor Unitário</label>
                <input type="text" class="form-control" placeholder="Valor unitário" name="valor_unitario" id="valor_unitario">
            </div>   
            <div class="form-group">
                <label for="first-name">CST</label>
                <input type="text" class="form-control" placeholder="CST" name="cst" id="cst">
            </div>   
            <div class="form-group">
                <label for="first-name">CFOP</label>
                <input type="text" class="form-control" placeholder="CFOP" name="cfop" id="cfop">
            </div>   

            <div class="form-group">
                <label for="first-name">Aliquota ICMS (%)</label>
                <input type="text" class="form-control" placeholder="Aliquota ICMS" name="aliq_icms" id="aliq_icms">
            </div>  
            <div class="form-group">
                <label for="first-name">Aliquota PIS (%)</label>
                <input type="text" class="form-control" placeholder="Aliquota PIS" name="aliq_pis" id="aliq_pis">
            </div>  
            <div class="form-group">
                <label for="first-name">Aliquota COFINS (%)</label>
                <input type="text" class="form-control" placeholder="Aliquota COFINS" name="aliq_cofins" id="aliq_cofins">
            </div>  

            <div class="form-group">
                <label for="first-name">Valor Aprox. Tributos (Lei 12.741/12)</label>
                <input type="text" class="form-control" placeholder="Valor Aprox. Tributos" name="valor_aprox_tributos" id="valor_aprox_tributos">
            </div>   

            <div class="clearfix"></div>
            <button type="submit" class="btn btn-info btn-lg btn-responsive" id="search"> <span class="glyphicon glyphicon-send"></span> Emitir</button>
        </form>
    </div>



</body></html>