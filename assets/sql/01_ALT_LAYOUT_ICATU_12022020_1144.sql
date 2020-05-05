/**************************************************************************************************************
Objetivo:   Alteração do layout da carga de Capitalização para o novo layout da Icatu. 
            Também realizado as alterações do Sorteio da Quero Quero.
Versão:     1.0.0
Branch:     econnects/relacionada: capitalizacao_alteracao_nomenclatura_icatu
Data:       12/02/2020
Autor:      Cláudio Nakayama

******* A FAZER Registro Detalhe: *******
- Campo 07 (Código do Cliente): O mesmo número de proposta só pode utilizado 2 vezes dentro do mesmo mês;
  Resposta: Quem controla a emissão é o representante. Verificar esta regra com o Gillian
**************************************************************************************************************/
#### Script para formatação do novo layout da ICATU ####
USE sisconnects;
# Alteração: Altera o layout para ambiente de produção
UPDATE `integracao` SET `ambiente`='P' WHERE `integracao_id`='152';
UPDATE `integracao` SET `ambiente`='P' WHERE `integracao_id`='197';

# Alteração: Voltar o padrao de calculo da forma padrão
UPDATE `integracao` SET `script_sql`='/*Específico para o ID: 152*/\nselect *\nfrom (\n	select ppp.slug_plano, \n           ifnull(ld.qtde_env_sorteio, 0) qtde_env_sorteio, \n           /*alteracao quero quero capitalizacao inicio*/\n           /*TIMESTAMPDIFF(MONTH, ifnull(ag.data_ini_vigencia,ae.data_ini_vigencia), date_add(ifnull(ag.data_fim_vigencia,ae.data_fim_vigencia), interval 1 day)) qtde_sorteio,*/\n           case when cap.tipo_qnt_sorteio = 1 then\n                     ifnull(TIMESTAMPDIFF(MONTH, ifnull(ag.data_ini_vigencia,ae.data_ini_vigencia), date_add(ifnull(ag.data_fim_vigencia,ae.data_fim_vigencia), interval 1 day)),0)\n                else \n                     ifnull(cap.qnt_sorteio,0)\n		   end as qtde_sorteio,           \n           /*alteracao quero quero capitalizacao final*/\n           p.pedido_id, \n           cap.capitalizacao_id, \n           cap.num_remessa, \n           cap.nome as produto, \n           pr.parceiro_id, \n           pr.nome as parceiro, \n           pr.cnpj, \n           cl.cliente_id, \n           cl.razao_nome as cliente, \n           cl.cnpj_cpf, \n           IFNULL(DATE(NOW()),IFNULL(ld.data_sorteio, cst.data_sorteio)) AS data_sorteio, \n           /*IFNULL(ld.serie,0)+1*/ 1 as num_serie, \n           cst.numero as num_sorte, \n		   /*alteracao quero quero capitalizacao inicio*/\n           /*cap.valor_custo_titulo as vlr_custo, Retirado*/\n           /*IF(cap.tipo_custo = 1, 0.00, if(ppp.slug_plano = \'residencial_basico\', 0.6005993000, 1.2011986000)) valor_custo_titulo, Alterado*/ \n           IF(cap.tipo_custo = 1, 0.00,case when cp.custo > 0 then cp.custo else cap.valor_custo_titulo end) as valor_custo_titulo,\n           /*alteracao quero quero capitalizacao final*/\n           cap.valor_sorteio\n	from apolice a \n	join pedido p on a.pedido_id = p.pedido_id\n	join cotacao ct on p.cotacao_id = ct.cotacao_id\n	join parceiro pc on a.parceiro_id = pc.parceiro_id\n	join produto_parceiro_plano ppp on a.produto_parceiro_plano_id = ppp.produto_parceiro_plano_id\n	join produto_parceiro pp on ppp.produto_parceiro_id = pp.produto_parceiro_id\n	join parceiro pr on pp.parceiro_id = pr.parceiro_id\n	join produto_parceiro_capitalizacao ppc on ppp.produto_parceiro_id = ppc.produto_parceiro_id\n	join capitalizacao cap on ppc.capitalizacao_id = cap.capitalizacao_id\n	join capitalizacao_serie caps on cap.capitalizacao_id = caps.capitalizacao_id\n	join capitalizacao_serie_titulo cst on caps.capitalizacao_serie_id = cst.capitalizacao_serie_id and p.pedido_id = cst.pedido_id\n	join cobertura_plano cp on ppp.produto_parceiro_plano_id = cp.produto_parceiro_plano_id\n	join cobertura c on cp.cobertura_id = c.cobertura_id\n	join apolice_cobertura ac on a.apolice_id = ac.apolice_id and cp.cobertura_plano_id = ac.cobertura_plano_id\n	join apolice_status ast on a.apolice_status_id = ast.apolice_status_id\n	join pedido_status ps on p.pedido_status_id = ps.pedido_status_id\n	join cotacao_status cs on ct.cotacao_status_id = cs.cotacao_status_id\n	join cliente cl on ct.cliente_id = cl.cliente_id\n	left join apolice_generico ag on a.apolice_id = ag.apolice_id\n	left join apolice_equipamento ae on a.apolice_id = ae.apolice_id\n	left join (\n		select parceiro_id, cliente_id, serie, num_sorte, MAX(data_sorteio) data_sorteio, COUNT(1) qtde_env_sorteio\n		from (\n			select i.parceiro_id, d.chave, @pipe1 := LOCATE(\'|\', d.chave, 1) p1\n				, @pipe2 := LOCATE(\'|\', d.chave, @pipe1+1) p2\n				, @pipe3 := LOCATE(\'|\', d.chave, @pipe2+1) p3\n				, substring(d.chave, 1, @pipe1-1) cliente_id\n				, substring(d.chave, @pipe1+1, @pipe2-@pipe1-1) serie\n				, substring(d.chave, @pipe2+1, @pipe3-@pipe2-1) num_sorte\n				, STR_TO_DATE(substring(d.chave, @pipe3+1, length(d.chave) - @pipe3),\'%d%m%Y\') data_sorteio\n			from integracao_log_detalhe d\n			join integracao_log l on d.integracao_log_id = l.integracao_log_id\n			join integracao i on l.integracao_id = i.integracao_id\n			where 1\n			and d.deletado = 0 and l.deletado = 0 and i.deletado = 0\n			and l.integracao_log_status_id = 4\n			and i.slug_group = \'sulacap-ativacao\'\n			and i.parceiro_id = {parceiro_id} /*Retirado valor fixo*/\n		) x \n		group by parceiro_id, cliente_id, serie, num_sorte\n	) ld on a.parceiro_id = ld.parceiro_id AND cl.cliente_id = ld.cliente_id AND ld.num_sorte = cst.numero\n	where a.deletado = 0 and p.deletado = 0 and ppp.deletado = 0 and cp.deletado = 0 and ppc.deletado = 0\n	  and cap.deletado = 0 and caps.deletado = 0 and cst.deletado = 0 and cst.ativo = 1\n	  and date_format(ifnull(ag.data_adesao,ae.data_adesao), \'%Y%m\') <= date_format(date_add(now(), interval -1 month), \'%Y%m\')\n	  and c.slug IN(\'sorteio_mensal\',\'capitalizacao_nro_sorte\')\n	  and ast.slug = \'ativa\'\n	  and ps.slug = \'pagamento_confirmado\'\n     and cs.slug = \'finalizada\'\n     and pc.parceiro_id = {parceiro_id} /*Retirado valor fixo*/\n     and ppp.produto_parceiro_plano_id = 114\n     and a.num_apolice <> \'NNNNNNNNNN\'\n) x\nwhere qtde_env_sorteio < qtde_sorteio;' 
 WHERE `integracao_id`='152';
UPDATE `integracao` SET `script_sql`='/*Específico para o ID: 197*/\nselect *\nfrom (\n	select ppp.slug_plano, \n           ifnull(ld.qtde_env_sorteio, 0) qtde_env_sorteio, \n           /*alteracao quero quero capitalizacao inicio*/\n           /*TIMESTAMPDIFF(MONTH, ifnull(ag.data_ini_vigencia,ae.data_ini_vigencia), date_add(ifnull(ag.data_fim_vigencia,ae.data_fim_vigencia), interval 1 day)) qtde_sorteio,*/\n           case when cap.tipo_qnt_sorteio = 1 then\n                     ifnull(TIMESTAMPDIFF(MONTH, ifnull(ag.data_ini_vigencia,ae.data_ini_vigencia), date_add(ifnull(ag.data_fim_vigencia,ae.data_fim_vigencia), interval 1 day)),0)\n                else \n                     ifnull(cap.qnt_sorteio,0)\n		   end as qtde_sorteio,           \n           /*alteracao quero quero capitalizacao final*/\n           p.pedido_id, \n           cap.capitalizacao_id, \n           cap.num_remessa, \n           cap.nome as produto, \n           pr.parceiro_id, \n           pr.nome as parceiro, \n           pr.cnpj, \n           cl.cliente_id, \n           cl.razao_nome as cliente, \n           cl.cnpj_cpf, \n           IFNULL(DATE(NOW()),IFNULL(ld.data_sorteio, cst.data_sorteio)) AS data_sorteio, \n           /*IFNULL(ld.serie,0)+1*/ 1 as num_serie, \n           cst.numero as num_sorte, \n		     /*alteracao quero quero capitalizacao inicio*/\n           /*cap.valor_custo_titulo as vlr_custo, Retirado*/\n           /*IF(cap.tipo_custo = 1, 0.00, if(ppp.slug_plano = \'residencial_basico\', 0.6005993000, 1.2011986000)) valor_custo_titulo, Alterado*/ \n           IF(cap.tipo_custo = 1, 0.00,case when cp.custo > 0 then cp.custo else cap.valor_custo_titulo end) as valor_custo_titulo,\n           /*alteracao quero quero capitalizacao final*/\n           cap.valor_sorteio\n	from apolice a \n	join pedido p on a.pedido_id = p.pedido_id\n	join cotacao ct on p.cotacao_id = ct.cotacao_id\n	join parceiro pc on a.parceiro_id = pc.parceiro_id\n	join produto_parceiro_plano ppp on a.produto_parceiro_plano_id = ppp.produto_parceiro_plano_id\n	join produto_parceiro pp on ppp.produto_parceiro_id = pp.produto_parceiro_id\n	join parceiro pr on pp.parceiro_id = pr.parceiro_id\n	join produto_parceiro_capitalizacao ppc on ppp.produto_parceiro_id = ppc.produto_parceiro_id\n	join capitalizacao cap on ppc.capitalizacao_id = cap.capitalizacao_id\n	join capitalizacao_serie caps on cap.capitalizacao_id = caps.capitalizacao_id\n	join capitalizacao_serie_titulo cst on caps.capitalizacao_serie_id = cst.capitalizacao_serie_id and p.pedido_id = cst.pedido_id\n	join cobertura_plano cp on ppp.produto_parceiro_plano_id = cp.produto_parceiro_plano_id\n	join cobertura c on cp.cobertura_id = c.cobertura_id\n	join apolice_cobertura ac on a.apolice_id = ac.apolice_id and cp.cobertura_plano_id = ac.cobertura_plano_id\n	join apolice_status ast on a.apolice_status_id = ast.apolice_status_id\n	join pedido_status ps on p.pedido_status_id = ps.pedido_status_id\n	join cotacao_status cs on ct.cotacao_status_id = cs.cotacao_status_id\n	join cliente cl on ct.cliente_id = cl.cliente_id\n	left join apolice_generico ag on a.apolice_id = ag.apolice_id\n	left join apolice_equipamento ae on a.apolice_id = ae.apolice_id\n	left join (\n		select parceiro_id, cliente_id, serie, num_sorte, MAX(data_sorteio) data_sorteio, COUNT(1) qtde_env_sorteio\n		from (\n			select i.parceiro_id, d.chave, @pipe1 := LOCATE(\'|\', d.chave, 1) p1\n				, @pipe2 := LOCATE(\'|\', d.chave, @pipe1+1) p2\n				, @pipe3 := LOCATE(\'|\', d.chave, @pipe2+1) p3\n				, substring(d.chave, 1, @pipe1-1) cliente_id\n				, substring(d.chave, @pipe1+1, @pipe2-@pipe1-1) serie\n				, substring(d.chave, @pipe2+1, @pipe3-@pipe2-1) num_sorte\n				, STR_TO_DATE(substring(d.chave, @pipe3+1, length(d.chave) - @pipe3),\'%d%m%Y\') data_sorteio\n			from integracao_log_detalhe d\n			join integracao_log l on d.integracao_log_id = l.integracao_log_id\n			join integracao i on l.integracao_id = i.integracao_id\n			where 1\n			and d.deletado = 0 and l.deletado = 0 and i.deletado = 0\n			and l.integracao_log_status_id = 4\n			and i.slug_group = \'sulacap-ativacao\'\n			and i.parceiro_id = {parceiro_id} /*Retirado valor fixo*/\n		) x \n		group by parceiro_id, cliente_id, serie, num_sorte\n	) ld on a.parceiro_id = ld.parceiro_id AND cl.cliente_id = ld.cliente_id AND ld.num_sorte = cst.numero\n	where a.deletado = 0 and p.deletado = 0 and ppp.deletado = 0 and cp.deletado = 0 and ppc.deletado = 0\n	and cap.deletado = 0 and caps.deletado = 0 and cst.deletado = 0 and cst.ativo = 1\n	and date_format(ifnull(ag.data_adesao,ae.data_adesao), \'%Y%m\') <= date_format(date_add(now(), interval -1 month), \'%Y%m\')\n	and c.slug IN(\'sorteio_mensal\',\'capitalizacao_nro_sorte\')\n	and ast.slug = \'ativa\'\n	and ps.slug = \'pagamento_confirmado\'\n	and cs.slug = \'finalizada\'\n    and pc.parceiro_id = {parceiro_id} /*Retirado valor fixo*/\n    and ppp.produto_parceiro_plano_id = 115\n    and a.num_apolice <> \'NNNNNNNNNN\'\n) x\nwhere qtde_env_sorteio < qtde_sorteio;' 
 WHERE `integracao_id`='197'; 

# Correção >> Registro Header: Campo 02 (Tipo do Arquivo): Este campo deve ser preenchido com a informação “ATIVOS”;
UPDATE integracao_layout SET str_pad = 0 WHERE integracao_id = 103 AND tipo = 'H' AND ordem = 1;
UPDATE integracao_layout SET str_pad = 0 WHERE integracao_id = 120 AND tipo = 'H' AND ordem = 1;
UPDATE integracao_layout SET str_pad = 0 WHERE integracao_id = 152 AND tipo = 'H' AND ordem = 1;
UPDATE integracao_layout SET str_pad = 0 WHERE integracao_id = 196 AND tipo = 'H' AND ordem = 1;
UPDATE integracao_layout SET str_pad = 0 WHERE integracao_id = 197 AND tipo = 'H' AND ordem = 1;

#Alteração >> Campo 07 (Produto): Este campo deve ser preenchido com o código da negociação da Icatu;
UPDATE `integracao_layout` SET `nome_banco`='', `valor_padrao`='8968                ', `qnt_valor_padrao`='20', `str_pad`='1' WHERE integracao_id = 103 AND tipo = 'H' AND ordem = 6;
UPDATE `integracao_layout` SET `nome_banco`='', `valor_padrao`='8976                ', `qnt_valor_padrao`='20', `str_pad`='1' WHERE integracao_id = 120 AND tipo = 'H' AND ordem = 6;
UPDATE `integracao_layout` SET `nome_banco`='', `valor_padrao`='9334                ', `qnt_valor_padrao`='20', `str_pad`='1' WHERE integracao_id = 152 AND tipo = 'H' AND ordem = 6;
UPDATE `integracao_layout` SET `nome_banco`='', `valor_padrao`='9309                ', `qnt_valor_padrao`='20', `str_pad`='1' WHERE integracao_id = 196 AND tipo = 'H' AND ordem = 6;
UPDATE `integracao_layout` SET `nome_banco`='', `valor_padrao`='9335                ', `qnt_valor_padrao`='20', `str_pad`='1' WHERE integracao_id = 197 AND tipo = 'H' AND ordem = 6;

#Alteração >> Campo 03 (Número de Sorteio): Este campo deve possuir tamanho 6
UPDATE `integracao_layout` SET `tamanho`='6', `inicio`='5', `fim`='10', qnt_valor_padrao = '6' WHERE integracao_id = 103 AND tipo = 'D' AND ordem = 2;
UPDATE `integracao_layout` SET                       `inicio`='11', `fim`='52' WHERE integracao_id = 103 AND tipo = 'D' AND ordem = 3;
UPDATE `integracao_layout` SET                       `inicio`='53', `fim`='67' WHERE integracao_id = 103 AND tipo = 'D' AND ordem = 4;
UPDATE `integracao_layout` SET                       `inicio`='68', `fim`='75' WHERE integracao_id = 103 AND tipo = 'D' AND ordem = 5;
UPDATE `integracao_layout` SET                       `inicio`='76', `fim`='86' WHERE integracao_id = 103 AND tipo = 'D' AND ordem = 6;
UPDATE `integracao_layout` SET                       `inicio`='87', `fim`='96' WHERE integracao_id = 103 AND tipo = 'D' AND ordem = 7;
UPDATE `integracao_layout` SET `tamanho`='6', `inicio`='5', `fim`='10', qnt_valor_padrao = '6' WHERE integracao_id = 120 AND tipo = 'D' AND ordem = 2;
UPDATE `integracao_layout` SET                       `inicio`='11', `fim`='52' WHERE integracao_id = 120 AND tipo = 'D' AND ordem = 3;
UPDATE `integracao_layout` SET                       `inicio`='53', `fim`='67' WHERE integracao_id = 120 AND tipo = 'D' AND ordem = 4;
UPDATE `integracao_layout` SET                       `inicio`='68', `fim`='75' WHERE integracao_id = 120 AND tipo = 'D' AND ordem = 5;
UPDATE `integracao_layout` SET                       `inicio`='76', `fim`='86' WHERE integracao_id = 120 AND tipo = 'D' AND ordem = 6;
UPDATE `integracao_layout` SET                       `inicio`='87', `fim`='96' WHERE integracao_id = 120 AND tipo = 'D' AND ordem = 7;
UPDATE `integracao_layout` SET `tamanho`='6', `inicio`='5', `fim`='10', qnt_valor_padrao = '6' WHERE integracao_id = 152 AND tipo = 'D' AND ordem = 2;
UPDATE `integracao_layout` SET                       `inicio`='11', `fim`='52' WHERE integracao_id = 152 AND tipo = 'D' AND ordem = 3;
UPDATE `integracao_layout` SET                       `inicio`='53', `fim`='67' WHERE integracao_id = 152 AND tipo = 'D' AND ordem = 4;
UPDATE `integracao_layout` SET                       `inicio`='68', `fim`='75' WHERE integracao_id = 152 AND tipo = 'D' AND ordem = 5;
UPDATE `integracao_layout` SET                       `inicio`='76', `fim`='86' WHERE integracao_id = 152 AND tipo = 'D' AND ordem = 6;
UPDATE `integracao_layout` SET                       `inicio`='87', `fim`='96' WHERE integracao_id = 152 AND tipo = 'D' AND ordem = 7;
UPDATE `integracao_layout` SET `tamanho`='6', `inicio`='5', `fim`='10', qnt_valor_padrao = '6' WHERE integracao_id = 196 AND tipo = 'D' AND ordem = 2;
UPDATE `integracao_layout` SET                       `inicio`='11', `fim`='52' WHERE integracao_id = 196 AND tipo = 'D' AND ordem = 3;
UPDATE `integracao_layout` SET                       `inicio`='53', `fim`='67' WHERE integracao_id = 196 AND tipo = 'D' AND ordem = 4;
UPDATE `integracao_layout` SET                       `inicio`='68', `fim`='75' WHERE integracao_id = 196 AND tipo = 'D' AND ordem = 5;
UPDATE `integracao_layout` SET                       `inicio`='76', `fim`='86' WHERE integracao_id = 196 AND tipo = 'D' AND ordem = 6;
UPDATE `integracao_layout` SET                       `inicio`='87', `fim`='96' WHERE integracao_id = 196 AND tipo = 'D' AND ordem = 7;
UPDATE `integracao_layout` SET `tamanho`='6', `inicio`='5', `fim`='10', qnt_valor_padrao = '6' WHERE integracao_id = 197 AND tipo = 'D' AND ordem = 2;
UPDATE `integracao_layout` SET                       `inicio`='11', `fim`='52' WHERE integracao_id = 197 AND tipo = 'D' AND ordem = 3;
UPDATE `integracao_layout` SET                       `inicio`='53', `fim`='67' WHERE integracao_id = 197 AND tipo = 'D' AND ordem = 4;
UPDATE `integracao_layout` SET                       `inicio`='68', `fim`='75' WHERE integracao_id = 197 AND tipo = 'D' AND ordem = 5;
UPDATE `integracao_layout` SET                       `inicio`='76', `fim`='86' WHERE integracao_id = 197 AND tipo = 'D' AND ordem = 6;
UPDATE `integracao_layout` SET                       `inicio`='87', `fim`='96' WHERE integracao_id = 197 AND tipo = 'D' AND ordem = 7;

#Alteração >> Campo 08 (Valor unitário capitalização): Este campo deve utilizar 6 casas decimais / Campo 08 (Valor unitário capitalização): O valor informado está divergente do valor máximo permitido para esta negociação
UPDATE `integracao_layout` SET `formato`='registro|valor_custo_titulo|6|', `tamanho`='9', `fim`='95', `nome_banco`='valor_custo_titulo' WHERE integracao_id = 103 AND tipo = 'D' AND ordem = 7;
UPDATE `integracao_layout` SET `formato`='registro|valor_custo_titulo|6|', `tamanho`='9', `fim`='95', `nome_banco`='valor_custo_titulo' WHERE integracao_id = 120 AND tipo = 'D' AND ordem = 7;
UPDATE `integracao_layout` SET `formato`='registro|valor_custo_titulo|6|', `tamanho`='9', `fim`='95', `nome_banco`='valor_custo_titulo' WHERE integracao_id = 152 AND tipo = 'D' AND ordem = 7;
UPDATE `integracao_layout` SET `formato`='registro|valor_custo_titulo|6|', `tamanho`='9', `fim`='95', `nome_banco`='valor_custo_titulo' WHERE integracao_id = 196 AND tipo = 'D' AND ordem = 7;
UPDATE `integracao_layout` SET `formato`='registro|valor_custo_titulo|6|', `tamanho`='9', `fim`='95', `nome_banco`='valor_custo_titulo' WHERE integracao_id = 197 AND tipo = 'D' AND ordem = 7;

#Alteração: Após a linha do Registro Trailler, deve ser incluído mais uma linha sem nenhuma informação
SET @integracao_detalhe_id = (select integracao_detalhe_id from integracao_detalhe where integracao_id = 103 and tipo = 'T');   
SET @integracao_layout_id = (select max(integracao_layout_id) + 1 from integracao_layout);
INSERT INTO `integracao_layout` (`integracao_layout_id`, `integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `str_upper`, `deletado`, `criacao`, `alteracao_usuario_id`) VALUES (@integracao_layout_id, '103', @integracao_detalhe_id, 'T', '3', 'Quebra de Linha', 'Incluir Quebra de Linha no final do arquivo', '', 'C', '1', '1', '0', '0', '96', '96', '\n', '1', '1', '1', '0', '2020-02-11 00:00:00', 0);
SET @integracao_detalhe_id = (select integracao_detalhe_id from integracao_detalhe where integracao_id = 120 and tipo = 'T');   
SET @integracao_layout_id = (select max(integracao_layout_id) + 1 from integracao_layout);
INSERT INTO `integracao_layout` (`integracao_layout_id`, `integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `str_upper`, `deletado`, `criacao`, `alteracao_usuario_id`) VALUES (@integracao_layout_id, '120', @integracao_detalhe_id, 'T', '3', 'Quebra de Linha', 'Incluir Quebra de Linha no final do arquivo', '', 'C', '1', '1', '0', '0', '96', '96', '\n', '1', '1', '1', '0', '2020-02-11 00:00:00', 0);
SET @integracao_detalhe_id = (select integracao_detalhe_id from integracao_detalhe where integracao_id = 152 and tipo = 'T');   
SET @integracao_layout_id = (select max(integracao_layout_id) + 1 from integracao_layout);
INSERT INTO `integracao_layout` (`integracao_layout_id`, `integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `str_upper`, `deletado`, `criacao`, `alteracao_usuario_id`) VALUES (@integracao_layout_id, '152', @integracao_detalhe_id, 'T', '3', 'Quebra de Linha', 'Incluir Quebra de Linha no final do arquivo', '', 'C', '1', '1', '0', '0', '96', '96', '\n', '1', '1', '1', '0', '2020-02-11 00:00:00', 0);
SET @integracao_detalhe_id = (select integracao_detalhe_id from integracao_detalhe where integracao_id = 196 and tipo = 'T');   
SET @integracao_layout_id = (select max(integracao_layout_id) + 1 from integracao_layout);
INSERT INTO `integracao_layout` (`integracao_layout_id`, `integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `str_upper`, `deletado`, `criacao`, `alteracao_usuario_id`) VALUES (@integracao_layout_id, '196', @integracao_detalhe_id, 'T', '3', 'Quebra de Linha', 'Incluir Quebra de Linha no final do arquivo', '', 'C', '1', '1', '0', '0', '96', '96', '\n', '1', '1', '1', '0', '2020-02-11 00:00:00', 0);
SET @integracao_detalhe_id = (select integracao_detalhe_id from integracao_detalhe where integracao_id = 197 and tipo = 'T');   
SET @integracao_layout_id = (select max(integracao_layout_id) + 1 from integracao_layout);
INSERT INTO `integracao_layout` (`integracao_layout_id`, `integracao_id`, `integracao_detalhe_id`, `tipo`, `ordem`, `nome`, `descricao`, `formato`, `campo_tipo`, `tamanho`, `obrigatorio`, `campo_log`, `insert`, `inicio`, `fim`, `valor_padrao`, `qnt_valor_padrao`, `str_pad`, `str_upper`, `deletado`, `criacao`, `alteracao_usuario_id`) VALUES (@integracao_layout_id, '197', @integracao_detalhe_id, 'T', '3', 'Quebra de Linha', 'Incluir Quebra de Linha no final do arquivo', '', 'C', '1', '1', '0', '0', '96', '96', '\n', '1', '1', '1', '0', '2020-02-11 00:00:00', 0);

# Alteração: Mudar nomenclatura para "Ativos: ATIVOSCCCCSSSSSS.TXT"
UPDATE `integracao_layout` SET `formato`='ATIVOS8968', tamanho = 20 WHERE integracao_id = 103 AND tipo = 'F' AND ordem = 0;
UPDATE `integracao_layout` SET `formato`='ATIVOS8976', tamanho = 20 WHERE integracao_id = 120 AND tipo = 'F' AND ordem = 0;
UPDATE `integracao_layout` SET `formato`='ATIVOS9334', tamanho = 20 WHERE integracao_id = 152 AND tipo = 'F' AND ordem = 0;
UPDATE `integracao_layout` SET `formato`='ATIVOS9309', tamanho = 20 WHERE integracao_id = 196 AND tipo = 'F' AND ordem = 0;
UPDATE `integracao_layout` SET `formato`='ATIVOS9335', tamanho = 20 WHERE integracao_id = 197 AND tipo = 'F' AND ordem = 0;

# Inclusao de registro para reiniciar os arquivos para a numeração 000001 por causa da alteração da Icatu para os novos códigos
INSERT INTO `integracao_log` (`integracao_log_status_id`, `integracao_id`, `sequencia`, `processamento_inicio`, `processamento_fim`, `nome_arquivo`, `quantidade_registros`, `retorno`, `retorno_codigo`, `log_erro`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES ('1', '103', '0', '2020-02-12 10:40:00', '2020-02-12 10:40:01', 'NOVA_SEQUENCIA_ICATU', '0', '', '', '', '0', '2020-02-12 10:40:00', '0', '2020-02-12 10:40:00');
INSERT INTO `integracao_log` (`integracao_log_status_id`, `integracao_id`, `sequencia`, `processamento_inicio`, `processamento_fim`, `nome_arquivo`, `quantidade_registros`, `retorno`, `retorno_codigo`, `log_erro`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES ('1', '120', '0', '2020-02-12 10:40:00', '2020-02-12 10:40:01', 'NOVA_SEQUENCIA_ICATU', '0', '', '', '', '0', '2020-02-12 10:40:00', '0', '2020-02-12 10:40:00');                      
INSERT INTO `integracao_log` (`integracao_log_status_id`, `integracao_id`, `sequencia`, `processamento_inicio`, `processamento_fim`, `nome_arquivo`, `quantidade_registros`, `retorno`, `retorno_codigo`, `log_erro`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES ('1', '152', '0', '2020-02-12 10:40:00', '2020-02-12 10:40:01', 'NOVA_SEQUENCIA_ICATU', '0', '', '', '', '0', '2020-02-12 10:40:00', '0', '2020-02-12 10:40:00');
INSERT INTO `integracao_log` (`integracao_log_status_id`, `integracao_id`, `sequencia`, `processamento_inicio`, `processamento_fim`, `nome_arquivo`, `quantidade_registros`, `retorno`, `retorno_codigo`, `log_erro`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES ('1', '196', '0', '2020-02-12 10:40:00', '2020-02-12 10:40:01', 'NOVA_SEQUENCIA_ICATU', '0', '', '', '', '0', '2020-02-12 10:40:00', '0', '2020-02-12 10:40:00');
INSERT INTO `integracao_log` (`integracao_log_status_id`, `integracao_id`, `sequencia`, `processamento_inicio`, `processamento_fim`, `nome_arquivo`, `quantidade_registros`, `retorno`, `retorno_codigo`, `log_erro`, `deletado`, `criacao`, `alteracao_usuario_id`, `alteracao`) VALUES ('1', '197', '0', '2020-02-12 10:40:00', '2020-02-12 10:40:01', 'NOVA_SEQUENCIA_ICATU', '0', '', '', '', '0', '2020-02-12 10:40:00', '0', '2020-02-12 10:40:00');                      

# Alteração: Campo 02 (Quantidade de Registros): Este campo deve ser preenchido com o número total de linhas de Registro Detalhe
UPDATE `integracao_layout` SET `function`='app_integracao_generali_total_itens' WHERE tipo = 'T' AND integracao_id = 103 AND ordem = 1;
UPDATE `integracao_layout` SET `function`='app_integracao_generali_total_itens' WHERE tipo = 'T' AND integracao_id = 120 AND ordem = 1;
UPDATE `integracao_layout` SET `function`='app_integracao_generali_total_itens' WHERE tipo = 'T' AND integracao_id = 152 AND ordem = 1;
UPDATE `integracao_layout` SET `function`='app_integracao_generali_total_itens' WHERE tipo = 'T' AND integracao_id = 196 AND ordem = 1;
UPDATE `integracao_layout` SET `function`='app_integracao_generali_total_itens' WHERE tipo = 'T' AND integracao_id = 197 AND ordem = 1;