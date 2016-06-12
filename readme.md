[wordnet homepage](https://wordnet.princeton.edu/)  
a lexical database for english  

## about this project
now it's just a tool. query word not finish yet.  
even if i have done for query word, i don't want put it online. i think its not perfect.   
&lt;&lt;&lt;PS  
我是中国人，但这个项目貌似不需要写汉语，除了这行。所以说，不要说我装13。
i'm chinese, the ps just a joke.  
PS;
### [dict index source file format](https://wordnet.princeton.edu/wordnet/man/wndb.5WN.html#sect2)
## sql table structure
\#  | name | datatype | length/set | allow null | default |
------|------|------|------|------|------|------  
1 | id | INT | 11 | N | AUTO_INCREMENT  
2 | lemma | VARCHAR | 500 | N | no default  
3 | pos | ENUM | 'a','n','r','v' | N | no default  
4 | synset_cnt | INT | 11 | N | no default  
5 | ptr_cnt | INT | 11 | N | no default  
6 | ptr | VARCHAR | 700 | Y | NULL  
7 | sense_cnt | INT | 11 | N | no default  
8 | tagsense_cnt | INT | 11 | N | no default  
9 | offset | VARCHAR | 700 | Y | NULL  
### [dict data source file format](https://wordnet.princeton.edu/wordnet/man/wndb.5WN.html#sect3)
## sql table structure
\#  | name | datatype | length/set | allow null | default  
------|------|------|------|------|------|------  
1 | id | INT | 11 | N | AUTO_INCREMENT  
2 | offset | VARCHAR | 8 | N | no default  
3 | lex_filenum | INT | 11 | N | no default  
4 | ss_type | ENUM | 'a','n','r','s','v' | N | no default  
5 | w_cnt | INT | 11 | N | no default  
6 | word | TEXT |  | N | no default  
7 | p_cnt | INT | 11 | N | no default  
8 | ptr | TEXT  |   | Y | no default  
9 | f_cnt | INT | 11 | Y | NULL  
10 | frames | TEXT |   | Y | no default  
11 | definition | TEXT |   | N | no default  
12 | sentence | TEXT |   | Y | no default  

### statistics
\# | index | data  
------|------|------  
adj | 21499 | 18185  
adv | 4475 | 3625  
noun | 117353 | 92192  
verb | 115400 | 13789  
total | 155467 | 117791  
