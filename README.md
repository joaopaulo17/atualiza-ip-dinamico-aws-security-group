# Classe para atualizar o IP em um security group da AWS


Classe para atualizar o IP em um security group da AWS. Eu e minha equipe nos deparamos com a questão de trabalharmos em casa, onde nossos IPs são dinâmicos e trocam com frequência. Para não ter que adicionar o IP manualmente a cada vez que ele fosse trocado. Me basei no artigo do link abaixo: 
* [ajginteractive](http://www.ajginteractive.com/blog/keep-your-aws-ec2-security-group-updated-with-a-dynamic-ip-address)



## Getting Started

Esse projeto irá contar que você possua uma conta na amazon aws e conhecimentos básicos de programação PHP e da API AWS

### Prerequisites

 - Composer
 - PHP 7.1 ou superior
 - Usuário com as permissões a baixo

                "ec2:RevokeSecurityGroupIngress",
                "ec2:AuthorizeSecurityGroupEgress",
                "ec2:AuthorizeSecurityGroupIngress",
                "ec2:UpdateSecurityGroupRuleDescriptionsEgress",
                "ec2:RevokeSecurityGroupEgress",
                "ec2:ApplySecurityGroupsToClientVpnTargetNetwork",
                "ec2:UpdateSecurityGroupRuleDescriptionsIngress"
                "ec2:DescribeSecurityGroupReferences",
                "ec2:DescribeSecurityGroups"



### Installing

Clonar o projeto do github

depois executar o composer
```
php composer install
```

## Usage

```php
require 'atualizaip.php';

$atualiza = new atualizaip(-1,-1,'-1');
$acessoLocal = true;
var_dump($atualiza->atualizaIP($acessoLocal));
```
## Examples

### Foram criados dois exemplos de consumo da classe

* exemploLocalAccess.php (exemplo de uso local)
* exemploRemoteAccess.php (exemplo de uso em um servidor externo)



## Running the tests

Não foram implementados testes na ferramenta



## Built With

* [Visual Studio](https://visualstudio.microsoft.com/pt-br/) - IDE
* [GITHUB](https://github.com/joaopaulo17) - Versionamento
* [WAMP](http://www.wampserver.com/en/) - apache e php
* [AWS](https://docs.aws.amazon.com/AWSEC2/latest/APIReference) - API Reference


## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags). 

## Authors

* **João Paulo Marques** - *Initial work* - [GIT](https://github.com/joaopaulo17)


## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details


## Contributing

Créditos a ajginteractive pelo artigo onde me baseie
* [ajginteractive](http://www.ajginteractive.com/blog/keep-your-aws-ec2-security-group-updated-with-a-dynamic-ip-address)

## License
[MIT](https://choosealicense.com/licenses/mit/)

