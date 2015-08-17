# kakas-visualisasi
## Progress

 ### Done:
 1. Data Controller
 * Gather data information (get attributes with variable type, cardinality and quantity on each attribute)
 * Save the "data" to database (kakas-dataset)
 * Save metamodel to database (kakas-visualisasi.datasets/attributes)
 2. Recommendation System
 * Generate mappings
 * Factual visualization knowledge rating
 * Calculate rating based on available factor (only factual visualization for now)
 * Send visualization configuration (selected data, visualization and mapping) to frontend
 
 ### In Progress:
 * Frontend (showing the visualization based on data given by recommender system)
 
 ### To Do:
 * Support for data other than CSV
 * Make a better variable type prediction algorithm
 * Data selection (instance and attribute)
 * Support mapping for visualization that has n data variables (stacked barchart, multiple line chart, etc.)
 * Other rating factors (user and data information, user-shared knowledge)
