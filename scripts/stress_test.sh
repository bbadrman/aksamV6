#!/bin/bash

# Specify the input and output file paths
input_file="prospect.csv"
output_file="prospect_output.csv"

# Number of records to generate
num_records=6500

# Read the input CSV file
rows=()
IFS=$'\n' read -d '' -r -a rows < "$input_file"

# Duplicate and increment the ID for the desired number of records
last_id=$(tail -n 1 "$input_file" | cut -d ',' -f 1 | tr -d '"')
last_id=20000
new_rows=()

for ((i=1; i<=num_records; i++)); do
    new_id=$((last_id + i))
    new_row="${rows[1]}"
    new_row="\"${new_id}\",${new_row#*,}"
    new_rows+=("$new_row")
done
# Concatenate the original and new rows
modified_rows=("${rows[@]}" "${new_rows[@]}")

# Write the modified rows to the output CSV file
printf '%s\n' "${modified_rows[@]}" > "$output_file"