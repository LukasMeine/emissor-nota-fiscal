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
                <input type="text" class="form-control" name="cpf_destinatario" placeholder="CPF" id="cpf">
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

            <div class="clearfix"></div>
            <button type="submit" class="btn btn-info btn-lg btn-responsive" id="search"> <span class="glyphicon glyphicon-send"></span> Emitir</button>
        </form>
    </div>



</body></html>