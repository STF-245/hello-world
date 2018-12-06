#!/usr/bin/env python3
from matplotlib import pyplot as plt
import numpy as np
from sklearn.datasets import load_iris

data = load_iris()

features = data.data
feature_names = data.feature_names
target = data.target
target_names = data.target_names


labels = target_names[target]
plength = features[:,2]

is_setosa = (labels == 'setosa')

max_setosa = plength[is_setosa].max()
min_non_setosa = plength[~is_setosa].min()

print('Maximum of setosa: {}'.format(max_setosa))
print('Minimum of setosa: {}'.format(min_non_setosa))

for t in range(3):
	if t == 0:
		c = 'r'
		marker = '>'
	elif t == 1:
		c = 'g'
		marker = 'o'
	elif t == 2:
		c = 'b'
		marker = 'x'

	plt.scatter(features[target == t, 0], 
				features[target == t, 1],
				marker = marker,
				c = c)
plt.show()