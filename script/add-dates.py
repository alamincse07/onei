import pandas as pd
import json
import os
from glob import glob
filenames = glob('csvs/*.csv')


def processfile(f):
    df = pd.read_csv(f)
    df['Date'] = os.path.splitext(os.path.basename(f))[0]
    #print(df.info())
    return df


#processjson('1.json')

dataframes = [processfile(f) for f in filenames]


df = pd.concat(dataframes)


df.to_csv('partnerize.csv', index=False)
print(df.info)
