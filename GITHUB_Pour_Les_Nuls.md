## Pour récupérer une branche sur sa branche

Depuis ma nouvelle branche,
git pull <remote> <branch> 
c'est à dire
git pull origin nom_de_la_branche_à_merger


## Pour merger

1. Il faut pusher sa branche actuelle=nvelle fonctionnalité
2. Il faut basculer sur la branche sur laquelle on merge
3. Puis on merge :
` git merge maBrancheActuelle `
4. on peut ensuite effacer la branche avec
` git branch -d branche-de-travail`
Pour effacer la branch même si elle a pas été mergée
`git branch -D branche-de-travail`

5. Attention il faut pusher la branche pour mettre à jour le repo distant !

Attention , si je veux recupérer ce qui a été fait sur la branche dev , je dois :
1. sauver ma branche
2. me mettre dessus
3. merger dev dessus
   `git merge dev`

## Pour revenir en arrière

- En local, sur qqch qu'on a pas commité
`git stash`
- En local et distant, sur qqch qu'on a commité  
`git revert HEAD ^`
- Revenir à létat précédent un commit en particulier :  !! WARNING !!
( options --soft juste ce qu'il y a en distant, --medium distant + indexés --hard distant + indexés +local)
  
  `git reset notreCommitCible --hard`

## Pour récupérer le repo distant en entier puis effacer sa branche actuelle et la mettre à jour avec une branche fonctionnelle

- Récupérer le repo distant en entier : 
  `git fetch origin `
  
- Effacer sa branche actuelle et la mettre à jour avec une branche fonctionnelle :
 `git reset --hard origin/laBrancheFonctionnelle `
