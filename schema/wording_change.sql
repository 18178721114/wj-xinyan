#老年痴呆文案修改
update risk_wording_base set risk_name=replace(risk_name, 'Dementia', 'Cognitive impairment') where risk_key='dementia' and language='en-US'
update risk_wording_base set risk_suggest=replace(risk_suggest, 'dementia', 'cognitive impairment') where risk_key='dementia' and language='en-US'